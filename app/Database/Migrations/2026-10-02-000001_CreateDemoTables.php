<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateDemoTables extends Migration
{
    public function up()
    {
        $this->forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true], 'question_es' => ['type' => 'TEXT'], 'question_en' => ['type' => 'TEXT'], 'answer_es' => ['type' => 'TEXT'], 'answer_en' => ['type' => 'TEXT']]);
        $this->forge->addKey('id', true); $this->forge->createTable('hotel_faq');
        $this->forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true], 'call_sid' => ['type' => 'VARCHAR', 'constraint' => 80], 'locale' => ['type' => 'VARCHAR', 'constraint' => 2], 'state' => ['type' => 'VARCHAR', 'constraint' => 20], 'guest_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true], 'question_count' => ['type' => 'INTEGER', 'default' => 0], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true); $this->forge->addUniqueKey('call_sid'); $this->forge->createTable('demo_calls');
        $this->forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true], 'call_sid' => ['type' => 'VARCHAR', 'constraint' => 80], 'guest_name' => ['type' => 'VARCHAR', 'constraint' => 100], 'requested_date' => ['type' => 'VARCHAR', 'constraint' => 80], 'status' => ['type' => 'VARCHAR', 'constraint' => 20], 'created_at' => ['type' => 'DATETIME']]);
        $this->forge->addKey('id', true); $this->forge->createTable('demo_requests');
    }
    public function down()
    {
        $this->forge->dropTable('demo_requests'); $this->forge->dropTable('demo_calls'); $this->forge->dropTable('hotel_faq');
    }
}
