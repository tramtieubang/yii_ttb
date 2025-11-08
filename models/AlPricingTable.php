<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_pricing_table".
 *
 * @property int $id
 * @property int|null $profile_id ID hệ nhôm / profile (nếu có)
 * @property string $item_code Mã vật tư (VD: NHOM55, KINH08, PK01)
 * @property string $item_name Tên vật tư
 * @property string $unit Đơn vị tính
 * @property float $base_price Giá cơ bản
 * @property float $labor_cost Chi phí nhân công
 * @property float $profit_percent % lợi nhuận
 * @property string|null $note Ghi chú
 * @property string $created_at Ngày tạo
 * @property string $updated_at Ngày cập nhật
 *
 * @property AlProfiles $profile
 */
class AlPricingTable extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_pricing_table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_id', 'note'], 'default', 'value' => null],
            [['profit_percent'], 'default', 'value' => 0.00],
            [['profile_id'], 'integer'],
            [['item_code', 'item_name', 'unit'], 'required'],
            [['base_price', 'labor_cost', 'profit_percent'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_code', 'unit'], 'string', 'max' => 50],
            [['item_name'], 'string', 'max' => 255],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlProfiles::class, 'targetAttribute' => ['profile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => 'Profile ID',
            'item_code' => 'Item Code',
            'item_name' => 'Item Name',
            'unit' => 'Unit',
            'base_price' => 'Base Price',
            'labor_cost' => 'Labor Cost',
            'profit_percent' => 'Profit Percent',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(AlProfiles::class, ['id' => 'profile_id']);
    }

}
