<?php

use yii\db\Migration;

class m251003_083627_create_table_units extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo bảng units
        $this->createTable('{{%units}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->unique()->notNull(),
            'name' => $this->string(100)->notNull(),
            'note' => $this->string(255),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Thêm dữ liệu mẫu
        $this->batchInsert('{{%units}}', ['code', 'name'], [
            ['VIEN', 'Viên'],
            ['HOP', 'Hộp'],
            ['M2', 'Mét vuông'],
            ['THUNG', 'Thùng'],
        ]);
       
    }

    public function safeDown()
    {
        $this->dropTable('{{%units}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251003_083627_create_table_units cannot be reverted.\n";

        return false;
    }
    */
}
