<?php
namespace App\Controllers;
use App\Libraries\Conversation;
use Twilio\Security\RequestValidator;
use Twilio\TwiML\VoiceResponse;
class Voice extends BaseController
{
    private function valid(): bool
    {
        $token = (string) env('TWILIO_AUTH_TOKEN', '');
        $base = rtrim((string) env('TWILIO_PUBLIC_BASE_URL', ''), '/');
        if ($token === '' || $base === '') return false;
        $url = $base . '/' . ltrim($this->request->getUri()->getPath(), '/');
        $signature = $this->request->getHeaderLine('X-Twilio-Signature');
        return (new RequestValidator($token))->validate($signature, $url, $this->request->getPost());
    }
    private function xml(string $text, string $locale, bool $done = false)
    {
        $response = new VoiceResponse();
        $language = $locale === 'en' ? 'en-US' : 'es-MX';
        if ($done) $response->say($text, ['language' => $language]);
        else {
            $gather = $response->gather(['input' => 'speech', 'action' => rtrim((string) env('TWILIO_PUBLIC_BASE_URL'), '/') . '/voice/turn', 'method' => 'POST', 'speechTimeout' => 'auto', 'language' => $language]);
            $gather->say($text, ['language' => $language]);
            $response->redirect(rtrim((string) env('TWILIO_PUBLIC_BASE_URL'), '/') . '/voice/turn', ['method' => 'POST']);
        }
        return $this->response->setContentType('text/xml')->setBody((string) $response);
    }
    public function incoming()
    {
        if (!$this->valid()) return $this->response->setStatusCode(403);
        $sid = (string) $this->request->getPost('CallSid');
        if (!preg_match('/^CA[a-f0-9]{32}$/i', $sid)) return $this->response->setStatusCode(400);
        return $this->xml((new Conversation(db_connect()))->begin($sid), 'es');
    }
    public function turn()
    {
        if (!$this->valid()) return $this->response->setStatusCode(403);
        $sid = (string) $this->request->getPost('CallSid');
        if (!preg_match('/^CA[a-f0-9]{32}$/i', $sid)) return $this->response->setStatusCode(400);
        $turn = (new Conversation(db_connect()))->turn($sid, (string) $this->request->getPost('SpeechResult'));
        return $this->xml($turn['text'], $turn['locale'], $turn['done']);
    }
}
