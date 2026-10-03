<?php
use App\Database\Seeds\DemoSeeder;
use App\Libraries\Conversation;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/** @internal */
final class VoiceFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    protected $namespace = 'App';
    protected $seed = DemoSeeder::class;

    public function testEnglishFaqAndRequest(): void
    {
        $flow = new Conversation($this->db);
        $sid = 'SIM-test-english';
        self::assertStringContainsString('English', $flow->begin($sid));
        self::assertSame('en', $flow->turn($sid, 'English')['locale']);
        self::assertStringContainsString('complimentary', $flow->turn($sid, 'parking')['text']);
        self::assertStringContainsString('name', $flow->turn($sid, 'reservation')['text']);
        $flow->turn($sid, 'Alex Demo');
        $result = $flow->turn($sid, 'October 12');
        self::assertTrue($result['done']);
        self::assertStringContainsString('not a confirmed booking', $result['text']);
        self::assertSame(1, $this->db->table('demo_requests')->where('call_sid', $sid)->countAllResults());
    }

    public function testSpanishAndUnknownQuestion(): void
    {
        $flow = new Conversation($this->db);
        $sid = 'SIM-test-spanish';
        $flow->begin($sid);
        self::assertSame('es', $flow->turn($sid, 'español')['locale']);
        self::assertStringContainsString('gratuito', $flow->turn($sid, 'estacionamiento')['text']);
        self::assertStringContainsString('No tengo', $flow->turn($sid, 'precio de submarino')['text']);
    }

    public function testEmptyInputDoesNotAdvanceState(): void
    {
        $flow = new Conversation($this->db);
        $sid = 'SIM-test-empty';
        $flow->begin($sid);
        self::assertStringContainsString('No escuché', $flow->turn($sid, '')['text']);
        self::assertSame('language', $this->db->table('demo_calls')->where('call_sid', $sid)->get()->getRow('state'));
    }
}
