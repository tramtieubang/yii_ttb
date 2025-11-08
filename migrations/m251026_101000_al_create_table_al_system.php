<?php

use yii\db\Migration;

class m251026_101000_al_create_table_al_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%al_systems}}', [
            'id' => $this->primaryKey()->comment('ID chính'),
            'code' => $this->string(50)->notNull()->unique()->comment('Mã hệ nhôm (VD: XF55, PMA60, TP50)'),
            'name' => $this->string(255)->notNull()->comment('Tên hệ nhôm (VD: Xingfa 55, PMA 60, Topal 50...)'),
            'brand' => $this->string(100)->null()->comment('Thương hiệu nhôm (VD: Xingfa, PMA, Topal...)'),
            'origin' => $this->string(100)->null()->comment('Xuất xứ / Nước sản xuất (VD: Trung Quốc, Việt Nam, Malaysia)'),
            'thickness' => $this->decimal(4,2)->null()->comment('Độ dày trung bình (mm)'),
            'color' => $this->string(100)->null()->comment('Màu sắc tiêu chuẩn (VD: Ghi, Trắng sứ, Đen, Nâu cà phê)'),
            'surface_type' => $this->string(100)->null()->comment('Loại bề mặt (VD: sơn tĩnh điện, anod, phủ bóng)'),
            'description' => $this->text()->null()->comment('Mô tả chi tiết hệ nhôm'),
            'status' => $this->string(20)->defaultValue('active')->comment('Trạng thái: active/inactive'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Danh mục hệ nhôm (al_systems)"');

    }

    public function safeDown()
    {
        $this->dropTable('{{%al_systems}}');
    }
    
}
