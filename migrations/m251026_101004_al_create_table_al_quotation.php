<?php

use yii\db\Migration;

class m251026_101004_al_create_table_al_quotation extends Migration
{
    /**
     * {@inheritdoc}
     */
   public function safeUp()
    {
        $this->createTable('{{%al_quotations}}', [
            'id' => $this->primaryKey()->comment('ID chính'),
            'quotation_code' => $this->string(50)->notNull()->unique()->comment('Mã báo giá'),
            'customer_id' => $this->integer()->notNull()->comment('Khách hàng'),
            'project_name' => $this->string(255)->notNull()->comment('Tên công trình'),
            'quotation_date' => $this->date()->notNull()->comment('Ngày báo giá'),

            // 💰 Các cột tính toán giá
            'subtotal' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Tạm tính'),
            'discount' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Giảm giá'),
            'tax' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Thuế'),
            'total_amount' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Tổng cộng'),

            'status' => $this->string(50)->defaultValue('draft')->comment('Trạng thái báo giá'),
            'note' => $this->text()->null()->comment('Ghi chú'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Báo giá sản phẩm nhôm"');
    
        $this->addForeignKey('fk_quotation_customer', '{{%al_quotations}}', 'customer_id', '{{%customers}}', 'id', 'CASCADE' );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_quotation_customer', '{{%al_quotations}}');
        $this->dropTable('{{%al_quotations}}');
    }
}
