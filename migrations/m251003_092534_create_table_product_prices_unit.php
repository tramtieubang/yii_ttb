<?php

use yii\db\Migration;

class m251003_092534_create_table_product_prices_unit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Tạo bảng product_units
        $this->createTable('{{%product_prices_unit}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'price' => $this->decimal(15,2)->notNull(),
            'datetime' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Thêm khóa ngoại
        
        $this->addForeignKey(
            'fk-product_rices_unit-product_id',  // Tên khóa ngoại
            '{{%product_prices_unit}}',          // Bảng hiện tại (con)
            'product_id',                        // Cột trong bảng hiện tại
            '{{%products}}',                     // Bảng cha (tham chiếu tới)
            'id',                                // Cột trong bảng cha
            'CASCADE'                            // Hành động khi xóa/sửa bản ghi cha
        );

        $this->addForeignKey(
            'fk-product_rices_unit-unit_id',     // Tên khóa ngoại
            '{{%product_prices_unit}}',          // Bảng hiện tại (con)
            'unit_id',                           // Cột trong bảng hiện tại
            '{{%units}}',                        // Bảng cha (tham chiếu tới)
            'id',                                // Cột trong bảng cha
            'CASCADE'                            // Hành động khi xóa/sửa bản ghi cha
        );

        // Thêm dữ liệu mẫu
       $this->batchInsert('{{%product_prices_unit}}',
            ['product_id', 'unit_id', 'price', 'datetime', 'created_at', 'updated_at'],
            [
                [1, 1, 12000.00, '2025-10-01 08:00:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [1, 2, 15000.50, '2025-10-02 09:15:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [2, 1, 18000.75, '2025-10-03 10:30:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [2, 3, 20000.00, '2025-10-04 11:45:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [3, 2, 25000.25, '2025-10-05 13:00:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [3, 4, 30000.00, '2025-10-06 14:15:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [1, 3, 35000.75, '2025-10-07 15:30:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [2, 4, 40000.00, '2025-10-08 16:45:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [3, 1, 45000.50, '2025-10-09 18:00:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [1, 2, 50000.00, '2025-10-10 19:15:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [2, 3, 55000.25, '2025-10-11 20:30:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
                [3, 4, 60000.00, '2025-10-12 21:45:00', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-product_rices_unit-product_id', '{{%product_prices_unit}}');
        $this->dropForeignKey('fk-product_rices_unit-unit_id', '{{%product_prices_unit}}');
        $this->dropTable('{{%product_prices_unit}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251003_092534_create_table_product_prices_unit cannot be reverted.\n";

        return false;
    }
    */
}
