<?php

use yii\db\Migration;

class m251026_101002_al_create_table_al_aluminum_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    /**
     * Handles the creation of table `aluminum_materials`.
     * Bảng quản lý thông tin vật liệu nhôm (thanh nhôm đầu vào).
     */
    public function safeUp()
    {
        $this->createTable('{{%al_aluminum_materials}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->notNull()->comment('ID profile nhôm'),
            'code' => $this->string(50)->notNull()->comment('Mã vật liệu'),
            'name' => $this->string(255)->notNull()->comment('Tên vật liệu nhôm'),
            'length' => $this->integer()->notNull()->comment('Chiều dài (mm)'),
            'stock_quantity' => $this->integer()->notNull()->defaultValue(0)->comment('Số lượng tồn kho'),
            'stock_length' => $this->integer()->notNull()->defaultValue(0)->comment('Chiều dài tồn kho (mm)'), // ⚠️ thêm dòng này
            'unit_price' => $this->decimal(15, 2)->notNull()->defaultValue(0)->comment('Đơn giá'),
            'note' => $this->text()->null()->comment('Ghi chú'),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Danh mục vật liệu nhôm"');

        $this->addForeignKey('fk_material_profile', '{{%al_aluminum_materials}}', 'profile_id', '{{%al_profiles}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_material_profile', '{{%al_aluminum_materials}}');
        $this->dropTable('{{%al_aluminum_materials}}');
    }
}
