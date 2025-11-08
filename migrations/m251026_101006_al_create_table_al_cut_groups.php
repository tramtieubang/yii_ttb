<?php

use yii\db\Migration;

/**
 * Handles the creation of table `al_cut_groups`.
 * Bảng quản lý danh sách cắt nhôm cho từng đơn hàng.
 */

class m251026_101006_al_create_table_al_cut_groups extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%al_cut_groups}}', [
            'id' => $this->primaryKey()->comment('ID nhóm cắt'),
            'order_id' => $this->integer()->notNull()->comment('FK -> al_orders'),
            'material_id' => $this->integer()->notNull()->comment('FK -> al_aluminum_materials'),
            'cut_length' => $this->integer()->notNull()->comment('Chiều dài cần cắt (mm)'),
            'quantity' => $this->integer()->notNull()->defaultValue(1)->comment('Số lượng cần cắt'),
            'waste_length' => $this->integer()->defaultValue(0)->comment('Chiều dài hao hụt (mm)'),
            'total_used_length' => $this->integer()->defaultValue(0)->comment('Tổng chiều dài đã dùng (mm)'),
            
            'used_from_scrap_id' => $this->integer()->null()->comment('Nếu dùng nhôm dư (al_scrap_aluminum) để cắt, lưu ID nhôm dư này; nullable'), 
            // ⚡️ Thêm cột để lưu nhôm dư dùng cắt, nullable

            'note' => $this->string(255)->null()->comment('Ghi chú'),
             // ⚡️ Thêm hai dòng này:
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Nhóm cắt nhôm tối ưu"');

        $this->addForeignKey('fk_cut_order', '{{%al_cut_groups}}', 'order_id', '{{%al_orders}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_cut_material', '{{%al_cut_groups}}', 'material_id', '{{%al_aluminum_materials}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_cut_order', '{{%al_cut_groups}}');
        $this->dropForeignKey('fk_cut_material', '{{%al_cut_groups}}');
        $this->dropTable('{{%al_cut_groups}}');
    }

}
