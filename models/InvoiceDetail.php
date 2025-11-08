<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice_detail".
 *
 * @property int $id
 * @property int $invoice_id Khóa ngoại đến hóa đơn
 * @property int $product_price_unit_id Khóa ngoại đến bảng product_prices_unit
 * @property int $quantity Số lượng sản phẩm
 * @property float $unit_price Giá lưu
 * @property float $total Thành tiền
 * @property string|null $note Ghi chú
 *
 * @property Invoice $invoice
 * @property ProductPricesUnit $productPriceUnit
 */
class InvoiceDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notes'], 'default', 'value' => null],
            [['quantity'], 'default', 'value' => 1],
            [['total'], 'default', 'value' => 0.00],
            [['invoice_id', 'product_price_unit_id'], 'required'],
            [['invoice_id', 'product_price_unit_id', 'quantity'], 'integer'],
            [['unit_price', 'total'], 'number'],
            [['notes'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invoice_id' => 'id']],
            [['product_price_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductPricesUnit::class, 'targetAttribute' => ['product_price_unit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'product_price_unit_id' => 'Product Price Unit ID',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total' => 'Total',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['id' => 'invoice_id']);
    }

    /**
     * Gets query for [[ProductPriceUnit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductPriceUnit()
    {
        return $this->hasOne(ProductPricesUnit::class, ['id' => 'product_price_unit_id']);
    }

}
