<?php

use yii\db\Migration;

class m251026_101003_al_create_table_al_pricing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%al_pricing_table}}', [
           'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->null()->comment('ID hệ nhôm / profile (nếu có)'),
            'item_code' => $this->string(50)->notNull()->comment('Mã vật tư (VD: NHOM55, KINH08, PK01)'),
            'item_name' => $this->string(255)->notNull()->comment('Tên vật tư'),
            'unit' => $this->string(50)->notNull()->comment('Đơn vị tính'),
            'base_price' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Giá cơ bản'),
            'labor_cost' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Chi phí nhân công'),
            'profit_percent' => $this->decimal(5,2)->notNull()->defaultValue(0)->comment('% lợi nhuận'),
            'note' => $this->text()->null()->comment('Ghi chú'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Bảng giá chuẩn vật tư & nhân công"');

        $this->addForeignKey('fk_price_profile', '{{%al_pricing_table}}', 'profile_id', '{{%al_profiles}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_price_profile', '{{%al_pricing_table}}');
        $this->dropTable('{{%al_pricing_table}}');
    }

}
