<?php

namespace app\modules\products\models;

use app\models\Categories;
use app\models\ProductPricesUnit;
use app\models\Products;
use app\models\Units;
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
class ProductsForm extends Products
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
            [['category_id', 'name'], 'required'],
            [['category_id'], 'integer'],
            //[['price'], 'number'],
            [[ 'created_at', 'updated_at'], 'safe'],
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
            'id' => 'Mã số',
            'category_id' => 'Danh mục',
            'name' => 'Tên sản phẩm',
            'price' => 'Giá',
            'datetime' => 'Ngày giờ',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
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
    
   // Quan hệ đến bảng trung gian
    public function getProductPricesUnits()
    {
        return $this->hasMany(ProductPricesUnit::class, ['product_id' => 'id']);
    }

     // Quan hệ many-to-many đến Units
    public function getUnits()
    {
        return $this->hasMany(Units::class, ['id' => 'unit_id'])->via('productPricesUnits');
    }
    
    // Quan hệ many-to-many đến Units
   // Hỗ trợ sản phẩm lấy giá mới nhất theo đơn vị
    public function getLatestProductPricesUnit()
    {
        // Tạo subquery: lấy ngày mới nhất cho mỗi produce + unit
        $subQuery = (new \yii\db\Query())
            ->select([
                'product_id',
                'unit_id',
                'MAX(datetime) AS max_datetime'
            ])
            ->from('product_prices_unit')
            ->groupBy(['product_id', 'unit_id']);

        // Quan hệ hasMany: mỗi sản phẩm có thể có nhiều giá theo đơn vị
        return $this->hasMany(ProductPricesUnit::class, ['product_id' => 'id'])
            ->alias('ppu')
            ->innerJoin(
                ['t' => $subQuery],
                'ppu.product_id = t.product_id 
                AND ppu.unit_id = t.unit_id 
                AND ppu.datetime = t.max_datetime'
            )
            ->with('unit'); // nếu bạn có quan hệ getUnit() trong model ProducePricesUnit
    }

  
}
