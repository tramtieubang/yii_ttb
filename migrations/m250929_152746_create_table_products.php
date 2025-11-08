<?php

use yii\db\Migration;

class m250929_152746_create_table_products extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull()->comment('Product name'),
            'price' => $this->decimal(10,2)->notNull()->defaultValue(0),
            'datetime' => $this->dateTime()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // Add FK
        $this->addForeignKey(
            'fk-products-category_id',
            '{{%products}}',
            'category_id',
            '{{%categories}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Insert sample data
        $this->batchInsert('{{%products}}', ['category_id', 'name', 'price'], [
            [1, 'Smartphone', 500.00],
            [1, 'Laptop', 1200.00],
            [2, 'T-Shirt', 20.00],
            [2, 'Jeans', 45.00],
            [3, 'Novel', 15.00],
            [3, 'Dictionary', 30.00],
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-products-category_id', '{{%products}}');
        $this->dropTable('{{%products}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250929_152746_create_table_products cannot be reverted.\n";

        return false;
    }
    */
}
