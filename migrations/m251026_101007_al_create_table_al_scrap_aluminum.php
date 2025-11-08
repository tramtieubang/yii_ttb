<?php

use yii\db\Migration;

/**
 * Class m251026_101007_al_create_table_al_scrap_aluminum
 * 
 * Bảng lưu trữ thông tin nhôm vụn còn lại sau khi cắt,
 * bao gồm thông tin vật liệu gốc, nhóm cắt và tình trạng tái sử dụng.
 */

class m251026_101007_al_create_table_al_scrap_aluminum extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%al_scrap_aluminum}}', [
            'id' => $this->primaryKey()->comment('ID chính'),

            // Khóa ngoại đến nhóm cắt
            'cut_group_id' => $this->integer()->notNull()->comment('ID nhóm cắt'),

            // Khóa ngoại đến vật liệu nhôm
            'material_id' => $this->integer()->notNull()->comment('ID vật liệu nhôm (nguồn tạo ra nhôm vụn)'),

            // Dữ liệu chính
            'remaining_length' => $this->integer()->notNull()->comment('Chiều dài còn lại sau khi cắt (mm)'),
            'weight' => $this->decimal(10,3)->notNull()->comment('Trọng lượng nhôm vụn (kg)'),
            'is_reused' => $this->boolean()->defaultValue(false)->comment('Đã tái sử dụng chưa (0=chưa,1=đã)'),
            'note' => $this->string(255)->null()->comment('Ghi chú'),

            // Thời gian
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Nhôm vụn còn lại sau khi cắt"');

        // --- Khóa ngoại ---
        $this->addForeignKey(
            'fk_scrap_cutgroup',
            '{{%al_scrap_aluminum}}',
            'cut_group_id',
            '{{%al_cut_groups}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_scrap_material',
            '{{%al_scrap_aluminum}}',
            'material_id',
            '{{%al_aluminum_materials}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Xóa khóa ngoại trước khi xóa bảng
        $this->dropForeignKey('fk_scrap_cutgroup', '{{%al_scrap_aluminum}}');
        $this->dropForeignKey('fk_scrap_material', '{{%al_scrap_aluminum}}');

        // Xóa bảng
        $this->dropTable('{{%al_scrap_aluminum}}');
    }

}
