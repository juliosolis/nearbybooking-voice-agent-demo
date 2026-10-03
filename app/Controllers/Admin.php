<?php
namespace App\Controllers;
class Admin extends BaseController
{
    public function index()
    {
        $db = db_connect();
        return view('admin', [
            'faqs' => $db->table('hotel_faq')->orderBy('id')->get()->getResultArray(),
            'calls' => $db->table('demo_calls')->orderBy('id', 'DESC')->limit(20)->get()->getResultArray(),
            'requests' => $db->table('demo_requests')->orderBy('id', 'DESC')->get()->getResultArray(),
        ]);
    }
    public function saveFaq(int $id)
    {
        $values = [];
        foreach (['question_es', 'question_en', 'answer_es', 'answer_en'] as $field) {
            $value = trim((string) $this->request->getPost($field));
            if (mb_strlen($value) < 3 || mb_strlen($value) > 500) return redirect()->back()->with('error', 'Cada campo requiere entre 3 y 500 caracteres.');
            $values[$field] = $value;
        }
        db_connect()->table('hotel_faq')->where('id', $id)->update($values);
        return redirect()->to('/admin')->with('message', 'Respuesta actualizada.');
    }
    public function reviewRequest(int $id)
    {
        db_connect()->table('demo_requests')->where('id', $id)->update(['status' => 'reviewed']);
        return redirect()->to('/admin')->with('message', 'Solicitud marcada como revisada.');
    }
}
