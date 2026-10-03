<?php
namespace App\Libraries;
use CodeIgniter\Database\BaseConnection;
class Concierge
{
    public function __construct(private BaseConnection $db) {}
    public function answer(string $question, string $locale): string
    {
        $fallback = $locale === 'en' ? 'I do not have that information. Please contact the hotel.' : 'No tengo esa información. Consulta con el hotel.';
        $faqs = $this->db->table('hotel_faq')->get()->getResultArray();
        $language = $locale === 'en' ? 'en' : 'es';
        $key = mb_strtolower($question);
        foreach ($faqs as $faq) {
            foreach (preg_split('/\s+/', mb_strtolower($faq['question_' . $language])) as $word) {
                if (mb_strlen($word) >= 5 && str_contains($key, $word)) return $faq['answer_' . $language];
            }
        }
        $apiKey = (string) env('GEMINI_API_KEY', '');
        if ($apiKey === '') return $fallback;
        $facts = implode(' ', array_map(static fn($f) => $f['question_' . $language] . ': ' . $f['answer_' . $language], $faqs));
        try {
            $client = service('curlrequest', ['timeout' => 8]);
            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                'headers' => ['x-goog-api-key' => $apiKey, 'Content-Type' => 'application/json'],
                'json' => ['contents' => [['parts' => [['text' => "You are a fictional hotel concierge. Only use these facts: {$facts}. Answer briefly in {$language}. Never confirm availability, price or a booking. Question: {$question}"]]]]],
                'http_errors' => false,
            ]);
            if ($response->getStatusCode() !== 200) return $fallback;
            $body = json_decode($response->getBody(), true);
            return mb_substr(trim((string) ($body['candidates'][0]['content']['parts'][0]['text'] ?? $fallback)), 0, 500);
        } catch (\Throwable) { return $fallback; }
    }
}
