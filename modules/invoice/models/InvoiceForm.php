<?php

namespace app\modules\invoice\models;

use app\models\Customers;
use app\models\Invoice;
use app\models\InvoiceDetail;
use Yii;

class InvoiceForm extends Invoice
{
    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * Quy tắc kiểm tra dữ liệu
     */
    public function rules()
    {
        return [
            [['due_date', 'payment_method', 'notes', 'created_by'], 'default', 'value' => null],
            [['total_amount'], 'default', 'value' => 0.00],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],

            // Bắt buộc nhập
            [
                ['invoice_number', 'customer_id', 'issue_date'],
                'required',
                'message' => '{attribute} không được để trống.'
            ],

            [['customer_id', 'created_by'], 'integer'],
            [['issue_date', 'due_date', 'created_at', 'updated_at'], 'safe'],
            [['subtotal', 'discount_total', 'tax_total', 'total_amount'], 'number'],
            [['status', 'notes'], 'string'],
            [['invoice_number', 'payment_method', 'status'], 'string', 'max' => 50],

            // ✅ Kiểm tra trùng số hóa đơn (hợp lệ cho cả thêm mới và cập nhật)
            [
                'invoice_number',
                'unique',
                'targetClass' => self::class,
                'filter' => function ($query) {
                    if (!$this->isNewRecord) {
                        $query->andWhere(['not', ['id' => $this->id]]);
                    }
                },
                'message' => 'Số hóa đơn này đã tồn tại. Vui lòng nhập số khác.',
            ],

            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Mã số',
            'invoice_number' => 'Số hóa đơn',
            'customer_id' => 'Khách hàng',
            'issue_date' => 'Ngày lập hóa đơn',
            'due_date' => 'Hạn thanh toán',
            'subtotal' => 'Tổng trước thuế',
            'discount_total' => 'Tổng giảm giá',
            'tax_total' => 'Tổng thuế',
            'total_amount' => 'Tổng thanh toán',
            'status' => 'Trạng thái hóa đơn',
            'payment_method' => 'Phương thức thanh toán',
            'notes' => 'Ghi chú',
            'created_by' => 'Người tạo',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }

    public function getInvoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, ['invoice_id' => 'id']);
    }

    /**
     * Trạng thái ENUM
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'Nháp',
            self::STATUS_UNPAID => 'Chưa thanh toán',
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public function displayStatus()
    {
        return self::optsStatus()[$this->status] ?? '(Không xác định)';
    }

    // Helper nhanh cho kiểm tra trạng thái
    public function isStatusDraft() { return $this->status === self::STATUS_DRAFT; }
    public function setStatusToDraft() { $this->status = self::STATUS_DRAFT; }

    public function isStatusUnpaid() { return $this->status === self::STATUS_UNPAID; }
    public function setStatusToUnpaid() { $this->status = self::STATUS_UNPAID; }

    public function isStatusPaid() { return $this->status === self::STATUS_PAID; }
    public function setStatusToPaid() { $this->status = self::STATUS_PAID; }

    public function isStatusCancelled() { return $this->status === self::STATUS_CANCELLED; }
    public function setStatusToCancelled() { $this->status = self::STATUS_CANCELLED; }
}
