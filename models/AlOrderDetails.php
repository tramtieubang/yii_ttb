<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_order_details".
 *
 * @property int $id ID chi tiết
 * @property int $order_id Đơn hàng
 * @property int $material_id Vật liệu nhôm
 * @property float $cut_length Chiều dài cắt (mm)
 * @property int $quantity Số lượng
 * @property float $unit_price Đơn giá
 * @property float $amount Thành tiền
 * @property string|null $note Ghi chú
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlAluminumMaterials $material
 * @property AlOrders $order
 */
class AlOrderDetails extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_order_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['quantity'], 'default', 'value' => 1],
            [['amount'], 'default', 'value' => 0.00],
            [['order_id', 'material_id', 'cut_length'], 'required'],
            [['order_id', 'material_id', 'quantity'], 'integer'],
            [['cut_length', 'unit_price', 'amount'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlAluminumMaterials::class, 'targetAttribute' => ['material_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlOrders::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'material_id' => 'Material ID',
            'cut_length' => 'Cut Length',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'amount' => 'Amount',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Material]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(AlAluminumMaterials::class, ['id' => 'material_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(AlOrders::class, ['id' => 'order_id']);
    }

}
