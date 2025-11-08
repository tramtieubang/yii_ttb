<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $invoice_number Số hóa đơn duy nhất
 * @property int $customer_id Khách hàng liên kết
 * @property string $issue_date Ngày lập hóa đơn
 * @property string|null $due_date Ngày đến hạn thanh toán
 * @property float $subtotal Tổng tiền trước thuế và giảm giá
 * @property float|null $discount_total Tổng số tiền giảm giá
 * @property float|null $tax_total Tổng số tiền thuế
 * @property float $total_amount Tổng tiền sau thuế và giảm giá
 * @property string|null $status Trạng thái hóa đơn (nháp, chưa thanh toán, đã thanh toán, đã hủy)
 * @property string|null $payment_method Phương thức thanh toán (tiền mặt, chuyển khoản...)
 * @property string|null $notes Ghi chú hóa đơn
 * @property int|null $created_by Người tạo hóa đơn
 * @property string $created_at Ngày tạo
 * @property string $updated_at Ngày cập nhật
 *
 * @property Customers $customer
 */
class Invoice extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['due_date', 'payment_method', 'notes', 'created_by'], 'default', 'value' => null],
            [['total_amount'], 'default', 'value' => 0.00],
            [['status'], 'default', 'value' => 'draft'],
            [['invoice_number', 'customer_id', 'issue_date'], 'required'],
            [['customer_id', 'created_by'], 'integer'],
            [['issue_date', 'due_date', 'created_at', 'updated_at'], 'safe'],
            [['subtotal', 'discount_total', 'tax_total', 'total_amount'], 'number'],
            [['status', 'notes'], 'string'],
            [['invoice_number', 'payment_method','status'], 'string', 'max' => 50],
            [['invoice_number'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'customer_id' => 'Customer ID',
            'issue_date' => 'Issue Date',
            'due_date' => 'Due Date',
            'subtotal' => 'Subtotal',
            'discount_total' => 'Discount Total',
            'tax_total' => 'Tax Total',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'payment_method' => 'Payment Method',
            'notes' => 'Notes',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'draft',
            self::STATUS_UNPAID => 'unpaid',
            self::STATUS_PAID => 'paid',
            self::STATUS_CANCELLED => 'cancelled',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isStatusUnpaid()
    {
        return $this->status === self::STATUS_UNPAID;
    }

    public function setStatusToUnpaid()
    {
        $this->status = self::STATUS_UNPAID;
    }

    /**
     * @return bool
     */
    public function isStatusPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function setStatusToPaid()
    {
        $this->status = self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isStatusCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function setStatusToCancelled()
    {
        $this->status = self::STATUS_CANCELLED;
    }
}
