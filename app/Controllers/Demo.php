<?php
namespace App\Controllers;
use App\Libraries\Conversation;
class Demo extends BaseController
{
    public function index()
    {
        return view('demo', ['messages' => session('demo_messages') ?? [], 'sid' => session('demo_sid')]);
    }
    public function simulate()
    {
        $sid = session('demo_sid'); $conversation = new Conversation(db_connect());
        $messages = session('demo_messages') ?? [];
        if (!$sid || $this->request->getPost('reset')) {
            $sid = 'SIM' . bin2hex(random_bytes(12)); $messages = [['role' => 'agent', 'text' => $conversation->begin($sid)]];
        } else {
            $speech = mb_substr(trim((string) $this->request->getPost('speech')), 0, 300);
            $messages[] = ['role' => 'guest', 'text' => $speech];
            $turn = $conversation->turn($sid, $speech);
            $messages[] = ['role' => 'agent', 'text' => $turn['text']];
        }
        session()->set(['demo_sid' => $sid, 'demo_messages' => array_slice($messages, -16)]);
        return redirect()->to('/');
    }
}
