<?php
namespace App\Libraries;
use CodeIgniter\Database\BaseConnection;
class Conversation
{
    public function __construct(private BaseConnection $db) {}
    public function begin(string $sid): string
    {
        if (!$this->db->table('demo_calls')->where('call_sid', $sid)->countAllResults()) {
            $this->db->table('demo_calls')->insert(['call_sid' => $sid, 'locale' => 'es', 'state' => 'language', 'question_count' => 0, 'created_at' => date('Y-m-d H:i:s')]);
        }
        return 'Bienvenido a Hotel Mirador Azul. Para español, diga español. For English, say English.';
    }
    public function turn(string $sid, string $speech): array
    {
        $call = $this->db->table('demo_calls')->where('call_sid', $sid)->get()->getRowArray();
        if (!$call) return ['text' => $this->begin($sid), 'locale' => 'es', 'done' => false];
        $speech = mb_substr(trim($speech), 0, 300);
        $locale = $call['locale']; $state = $call['state']; $done = false;
        if ($speech === '') {
            $text = $locale === 'en' ? 'I did not hear you. Please try again.' : 'No escuché su respuesta. Intente otra vez.';
        } elseif ($state === 'language') {
            $locale = preg_match('/english|ingl[eé]s|two|dos/i', $speech) ? 'en' : 'es';
            $state = 'ready';
            $text = $locale === 'en' ? 'Ask about the hotel, or say reservation to send a request.' : 'Pregunte sobre el hotel o diga reserva para enviar una solicitud.';
        } elseif ($state === 'name') {
            if (mb_strlen($speech) < 2) {
                $text = $locale === 'en' ? 'Please say your name again.' : 'Repita su nombre, por favor.';
            } else {
                $call['guest_name'] = $speech; $state = 'date';
                $text = $locale === 'en' ? 'What date would you prefer?' : '¿Qué fecha prefiere?';
            }
        } elseif ($state === 'date') {
            $this->db->table('demo_requests')->insert(['call_sid' => $sid, 'guest_name' => $call['guest_name'], 'requested_date' => mb_substr($speech, 0, 80), 'status' => 'new', 'created_at' => date('Y-m-d H:i:s')]);
            $state = 'done'; $done = true;
            $text = $locale === 'en' ? 'Your demo request has been recorded. This is not a confirmed booking. Goodbye.' : 'Su solicitud de demostración quedó registrada. No es una reserva confirmada. Hasta luego.';
        } elseif ($state === 'done') {
            $done = true; $text = $locale === 'en' ? 'Goodbye.' : 'Hasta luego.';
        } elseif (preg_match('/reserv|book/i', $speech)) {
            $state = 'name';
            $text = $locale === 'en' ? 'Please say your name.' : 'Diga su nombre, por favor.';
        } elseif ((int) $call['question_count'] >= 5) {
            $done = true; $state = 'done';
            $text = $locale === 'en' ? 'This demo has reached its turn limit. Goodbye.' : 'Esta demostración alcanzó el límite de turnos. Hasta luego.';
        } else {
            $text = (new Concierge($this->db))->answer($speech, $locale);
            $call['question_count']++;
        }
        $this->db->table('demo_calls')->where('call_sid', $sid)->update(['locale' => $locale, 'state' => $state, 'guest_name' => $call['guest_name'], 'question_count' => $call['question_count']]);
        return ['text' => $text, 'locale' => $locale, 'done' => $done];
    }
}
