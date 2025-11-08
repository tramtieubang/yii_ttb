<?php

use yii\db\Migration;

class m251017_083653_create_table_invoice_detail extends Migration
{
    public function safeUp()
    {
        // Tạo bảng invoice_detail
        $this->createTable('{{%invoice_detail}}', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer()->notNull()->comment('Khóa ngoại đến hóa đơn'),
            'product_price_unit_id' => $this->integer()->notNull()->comment('Khóa ngoại đến bảng product_prices_unit'),
            'quantity' => $this->integer()->notNull()->defaultValue(1)->comment('Số lượng sản phẩm'),
            'unit_price' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Giá lưu'),
            'total' => $this->decimal(15,2)->notNull()->defaultValue(0)->comment('Thành tiền'),
            'notes' => $this->string(255)->null()->comment('Ghi chú'),
         ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Khóa ngoại đến bảng invoice
        $this->addForeignKey(
            'fk_invoice_detail_invoice',
            '{{%invoice_detail}}',
            'invoice_id',
            '{{%invoice}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Khóa ngoại đến bảng product_prices_unit
        $this->addForeignKey(
            'fk_invoice_detail_price_unit',
            '{{%invoice_detail}}',
            'product_price_unit_id',
            '{{%product_prices_unit}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_invoice_detail_price_unit', '{{%invoice_detail}}');
        $this->dropForeignKey('fk_invoice_detail_invoice', '{{%invoice_detail}}');
        $this->dropTable('{{%invoice_detail}}');
    }
}
