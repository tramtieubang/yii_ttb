<?php

use yii\db\Migration;

class m251026_101005_al_create_table_al_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%al_orders}}', [
            'id' => $this->primaryKey()->comment('ID đơn hàng'),
            'order_code' => $this->string(50)->notNull()->unique()->comment('Mã đơn hàng'),
            'customer_id' => $this->integer()->notNull()->comment('FK -> customers'),
            'quotation_id' => $this->integer()->null()->comment('FK -> al_quotations'),
            'order_date' => $this->date()->notNull()->comment('Ngày đặt hàng'),
            'status' => $this->string(50)->defaultValue('pending')->comment('Trạng thái đơn hàng'),
            'total_amount' => $this->decimal(15,2)->defaultValue(0)->comment('Tổng tiền đơn hàng'),
            'description' => $this->text()->null()->comment('Ghi chú / mô tả đơn hàng'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Đơn hàng thực tế"');

        $this->addForeignKey('fk_order_customer', '{{%al_orders}}', 'customer_id', '{{%customers}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_order_quotation', '{{%al_orders}}', 'quotation_id', '{{%al_quotations}}', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_order_customer', '{{%al_orders}}');
        $this->dropForeignKey('fk_order_quotation', '{{%al_orders}}');
        $this->dropTable('{{%al_orders}}');
    }

}
