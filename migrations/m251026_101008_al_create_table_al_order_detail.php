<?php

use yii\db\Migration;

class m251026_101008_al_create_table_al_order_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->createTable('{{%al_order_details}}', [
            'id' => $this->primaryKey()->comment('ID chi tiết'),
            'order_id' => $this->integer()->notNull()->comment('Đơn hàng'),
            'material_id' => $this->integer()->notNull()->comment('Vật liệu nhôm'),
            'cut_length' => $this->decimal(10,2)->notNull()->comment('Chiều dài cắt (mm)'),
            'quantity' => $this->integer()->notNull()->defaultValue(1)->comment('Số lượng'),

            // 🧮 thêm 2 cột này để khớp với dữ liệu seed
            'unit_price' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Đơn giá'),
            'amount' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Thành tiền'),

            'note' => $this->text()->null()->comment('Ghi chú'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Chi tiết đơn hàng cắt nhôm"');

        // Khóa ngoại
        $this->addForeignKey('fk_orderdetail_order', '{{%al_order_details}}', 'order_id', '{{%al_orders}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_orderdetail_material', '{{%al_order_details}}', 'material_id', '{{%al_aluminum_materials}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_orderdetail_order', '{{%al_order_details}}');
        $this->dropForeignKey('fk_orderdetail_material', '{{%al_order_details}}');
        $this->dropTable('{{%al_order_details}}');
    }

}
