<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class DemoSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('hotel_faq')->countAllResults()) return;
        $this->db->table('hotel_faq')->insertBatch([
            ['question_es' => 'horario de entrada', 'question_en' => 'check in time', 'answer_es' => 'La entrada es a partir de las tres de la tarde.', 'answer_en' => 'Check in starts at three P M.'],
            ['question_es' => 'estacionamiento', 'question_en' => 'parking', 'answer_es' => 'El estacionamiento para huéspedes es gratuito.', 'answer_en' => 'Guest parking is complimentary.'],
            ['question_es' => 'mascotas', 'question_en' => 'pets', 'answer_es' => 'Se admiten perros pequeños previa solicitud.', 'answer_en' => 'Small dogs are allowed on request.'],
        ]);
    }
}
