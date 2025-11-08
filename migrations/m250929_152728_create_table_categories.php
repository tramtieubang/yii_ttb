<?php

use yii\db\Migration;

class m250929_152728_create_table_categories extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Insert sample data
        $this->batchInsert('{{%categories}}', ['name', 'description'], [
            ['Điện thoại', 'Danh mục các loại điện thoại'],
            ['Laptop', 'Danh mục các loại laptop'],
            ['Máy tính bảng', 'Danh mục các loại tablet'],
            ['Phụ kiện', 'Danh mục các loại phụ kiện'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250929_152728_create_table_categories cannot be reverted.\n";

        return false;
    }
    */
}
