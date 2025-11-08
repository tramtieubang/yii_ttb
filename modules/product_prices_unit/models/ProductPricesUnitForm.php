<?php

namespace app\modules\product_prices_unit\models;

use app\models\ProductPricesUnit;
use app\models\Products;
use app\models\Units;
use Yii;

/**
 * This is the model class for table "product_prices_unit".
 *
 * @property int $id
 * @property int $product_id
 * @property int $unit_id
 * @property float $price
 * @property string $datetime
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Products $product
 * @property Units $unit
 */
class ProductPricesUnitForm extends ProductPricesUnit
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_prices_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'unit_id', 'price', 'datetime'], 'required'],
            [['product_id', 'unit_id'], 'integer'],
            //[['price'], 'number'],           
            [['datetime', 'created_at', 'updated_at'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Units::class, 'targetAttribute' => ['unit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
       return [
            'id' => 'Mã số',
            'product_id' => 'Sản phẩm',
            'unit_id' => 'Đơn vị tính',
            'price' => 'Giá bán',
            'datetime' => 'Ngày giờ áp dụng',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Unit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Units::class, ['id' => 'unit_id']);
    }

}
