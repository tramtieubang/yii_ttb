<?php

use yii\db\Migration;

class m251003_082508_create_table_customers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Khách hàng
        $this->createTable('{{%customers}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(100)->notNull()->unique(),
            'phone' => $this->string(20),
            'address' => $this->string(255),
            'note' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');


        // Thêm dữ liệu mẫu
        $this->batchInsert('{{%customers}}',
            ['name', 'email', 'phone', 'address'],
            [
                ['Nguyễn Văn A', 'vana@example.com', '0909123456', '123 Trần Hưng Đạo, Hà Nội'],
                ['Trần Thị B', 'thib@example.com', '0912345678', '456 Lê Lợi, TP.HCM'],
                ['Lê Văn C', 'vanc@example.com', '0987654321', '789 Nguyễn Huệ, Đà Nẵng'],
            ]
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%customers}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251003_082508_create_table_customers cannot be reverted.\n";

        return false;
    }
    */
}
