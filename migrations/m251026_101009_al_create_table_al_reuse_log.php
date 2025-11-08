<?php

use yii\db\Migration;

class m251026_101009_al_create_table_al_reuse_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%al_reuse_log}}', [
            'id' => $this->primaryKey()->comment('ID bản ghi tái sử dụng'),
            'scrap_id' => $this->integer()->notNull()->comment('FK -> al_scrap_aluminum'),
            'used_in_cut_group_id' => $this->integer()->notNull()->comment('FK -> al_cut_groups'),
            'reuse_length' => $this->decimal(10,2)->notNull()->comment('Chiều dài nhôm tái sử dụng (mm)'),
            'quantity' => $this->integer()->defaultValue(1)->comment('Số lượng nhôm tái sử dụng'),
            'note' => $this->string(255)->null()->comment('Ghi chú'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Lịch sử tái sử dụng nhôm dư"');

        // Khóa ngoại
        $this->addForeignKey('fk_reuse_scrap', '{{%al_reuse_log}}', 'scrap_id', '{{%al_scrap_aluminum}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_reuse_cutgroup', '{{%al_reuse_log}}', 'used_in_cut_group_id', '{{%al_cut_groups}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_reuse_scrap', '{{%al_reuse_log}}');
        $this->dropForeignKey('fk_reuse_cutgroup', '{{%al_reuse_log}}');
        $this->dropTable('{{%al_reuse_log}}');
    }

}
