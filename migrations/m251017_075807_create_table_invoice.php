<?php

use yii\db\Migration;

class m251017_075807_create_table_invoice extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%invoice}}', [
            'id' => $this->primaryKey(),
            'invoice_number' => $this->string(50)->notNull()->unique()->comment('Số hóa đơn duy nhất'),
            'customer_id' => $this->integer()->notNull()->comment('Khách hàng liên kết'),
            'issue_date' => $this->date()->notNull()->comment('Ngày lập hóa đơn'),
            'due_date' => $this->date()->null()->comment('Ngày đến hạn thanh toán'),
            'subtotal' => $this->decimal(12, 2)->notNull()->defaultValue(0.00)->comment('Tổng tiền trước thuế và giảm giá'),
            'discount_total' => $this->decimal(12, 2)->defaultValue(0.00)->comment('Tổng số tiền giảm giá'),
            'tax_total' => $this->decimal(12, 2)->defaultValue(0.00)->comment('Tổng số tiền thuế'),
            'total_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0.00)->comment('Tổng tiền sau thuế và giảm giá'),
            'status' => $this->string(50)->null()->comment('Trạng thái hóa đơn (nháp, chưa thanh toán, đã thanh toán, đã hủy)'),
            'payment_method' => $this->string(50)->null()->comment('Phương thức thanh toán (tiền mặt, chuyển khoản...)'),
            'notes' => $this->text()->null()->comment('Ghi chú hóa đơn'),
            'created_by' => $this->integer()->null()->comment('Người tạo hóa đơn'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Ngày tạo'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Ngày cập nhật'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Tạo index cho customer_id
        $this->createIndex(
            'idx-invoice-customer_id',
            '{{%invoice}}',
            'customer_id'
        );

        // Thêm khóa ngoại đến bảng customers
        $this->addForeignKey(
            'fk-invoice-customer_id',
            '{{%invoice}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'RESTRICT',  // Không cho xóa nếu có hóa đơn
            'CASCADE'    // Cập nhật nếu id khách hàng thay đổi
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-invoice-customer_id', '{{%invoice}}');
        $this->dropIndex('idx-invoice-customer_id', '{{%invoice}}');
        $this->dropTable('{{%invoice}}');
    }
    
}
