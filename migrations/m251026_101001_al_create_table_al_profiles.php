<?php

use yii\db\Migration;

class m251026_101001_al_create_table_al_profiles extends Migration
{
    /**
     * {@inheritdoc}
     */
   public function safeUp()
    {
        $this->createTable('{{%al_profiles}}', [
            'id' => $this->primaryKey()->comment('ID chính'),
            'system_id' => $this->integer()->notNull()->comment('Liên kết đến hệ nhôm (al_systems.id)'),
            'code' => $this->string(50)->notNull()->unique()->comment('Mã profile (VD: PRF001, XF55...)'),
            'name' => $this->string(255)->notNull()->comment('Tên profile nhôm'),
            'door_types' => $this->string(255)->null()->comment('Loại cửa áp dụng (mở quay, trượt, lùa...)'),
            'length' => $this->integer()->defaultValue(6000)->comment('Chiều dài cây nhôm (mm)'),
            'weight_per_meter' => $this->decimal(10,3)->null()->comment('Trọng lượng / mét (kg/m)'),
            'unit_price' => $this->decimal(15,2)->null()->comment('Đơn giá / mét (VNĐ/m)'),
            'image_url' => $this->string(255)->null()->comment('Đường dẫn hình ảnh minh họa'),
            'note' => $this->text()->null()->comment('Ghi chú'),
            'status' => $this->string(20)->defaultValue('active')->comment('Trạng thái: active/inactive'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Danh mục profile nhôm (al_profiles)"');

        // Tạo khóa ngoại liên kết tới bảng al_systems
        $this->addForeignKey('fk_al_profiles_system_id','{{%al_profiles}}','system_id','{{%al_systems}}','id','CASCADE','CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_al_profiles_system_id', '{{%al_profiles}}');
        $this->dropTable('{{%al_profiles}}');
    }
    
}
