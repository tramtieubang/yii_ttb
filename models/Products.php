<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name Product name
 * @property float $price
 * @property string $datetime
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Categories $category
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'default', 'value' => 0.00],
            [['category_id', 'name', 'datetime'], 'required'],
            [['category_id'], 'integer'],
            [['price'], 'number'],
            [['datetime', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'price' => 'Price',
            'datetime' => 'Datetime',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    public function getUnits()
    {
        return $this->hasMany(Units::class, ['id' => 'unit_id'])
            ->via('ProductPricesUnit');
    }
}
