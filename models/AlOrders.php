<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_orders".
 *
 * @property int $id ID đơn hàng
 * @property string $order_code Mã đơn hàng
 * @property int $customer_id FK -> customers
 * @property int|null $quotation_id FK -> al_quotations
 * @property string $order_date Ngày đặt hàng
 * @property string|null $status Trạng thái đơn hàng
 * @property float|null $total_amount Tổng tiền đơn hàng
 * @property string|null $description Ghi chú / mô tả đơn hàng
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlCutGroups[] $alCutGroups
 * @property AlOrderDetails[] $alOrderDetails
 * @property Customers $customer
 * @property AlQuotations $quotation
 */
class AlOrders extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'description'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['total_amount'], 'default', 'value' => 0.00],
            [['order_code', 'customer_id', 'order_date'], 'required'],
            [['customer_id', 'quotation_id'], 'integer'],
            [['order_date', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
            [['description'], 'string'],
            [['order_code', 'status'], 'string', 'max' => 50],
            [['order_code'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlQuotations::class, 'targetAttribute' => ['quotation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_code' => 'Order Code',
            'customer_id' => 'Customer ID',
            'quotation_id' => 'Quotation ID',
            'order_date' => 'Order Date',
            'status' => 'Status',
            'total_amount' => 'Total Amount',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AlCutGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlCutGroups()
    {
        return $this->hasMany(AlCutGroups::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[AlOrderDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlOrderDetails()
    {
        return $this->hasMany(AlOrderDetails::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Quotation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation()
    {
        return $this->hasOne(AlQuotations::class, ['id' => 'quotation_id']);
    }

}
