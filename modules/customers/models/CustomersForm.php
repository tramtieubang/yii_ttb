<?php

namespace app\modules\customers\models;

use app\models\Customers;
use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class CustomersForm extends Customers
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'address', 'note'], 'default', 'value' => null],
            [['name', 'email'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 100],
            //[['phone'], 'string', 'max' => 20],
            // Quy tắc cho số điện thoại
            ['phone', 'match', 
                'pattern' => '/^(0[0-9]{9}|(\+84)[0-9]{9})$/',
                'message' => 'Số điện thoại phải đúng định dạng (VD: 0901234567 hoặc +84901234567).'
            ],
            ['phone', 'string', 'max' => 15],

            [['address', 'note'], 'string', 'max' => 255],
            [['email'], 'unique'],
            ['email', 'email', 'message' => 'Địa chỉ email không hợp lệ.'],
            ['email', 'unique', 'message' => 'Email này đã được sử dụng.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Mã khách hàng',
            'name' => 'Họ và tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'note' => 'Ghi chú',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];

    }

}
