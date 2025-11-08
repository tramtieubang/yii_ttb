<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_profiles".
 *
 * @property int $id ID chính
 * @property int $system_id Liên kết đến hệ nhôm (al_systems.id)
 * @property string $code Mã profile (VD: PRF001, XF55...)
 * @property string $name Tên profile nhôm
 * @property string|null $door_types Loại cửa áp dụng (mở quay, trượt, lùa...)
 * @property int|null $length Chiều dài cây nhôm (mm)
 * @property float|null $weight_per_meter Trọng lượng / mét (kg/m)
 * @property float|null $unit_price Đơn giá / mét (VNĐ/m)
 * @property string|null $image_url Đường dẫn hình ảnh minh họa
 * @property string|null $note Ghi chú
 * @property string|null $status Trạng thái: active/inactive
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlAluminumMaterials[] $alAluminumMaterials
 * @property AlPricingTable[] $alPricingTables
 * @property AlSystems $system
 */
class AlProfiles extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_profiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['door_types', 'weight_per_meter', 'image_url', 'note'], 'default', 'value' => null],
            [['length'], 'default', 'value' => 6000],
            [['status'], 'default', 'value' => 'active'],
            [['system_id', 'code', 'name'], 'required'],
            [['system_id', 'length'], 'integer'],
            [['weight_per_meter'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 50],
            [['name', 'door_types', 'image_url'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['code'], 'unique'],
            [['system_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlSystems::class, 'targetAttribute' => ['system_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_id' => 'System ID',
            'code' => 'Code',
            'name' => 'Name',
            'door_types' => 'Door Types',
            'length' => 'Length',
            'weight_per_meter' => 'Weight Per Meter',
            'unit_price' => 'Unit Price',
            'image_url' => 'Image Url',
            'note' => 'Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AlAluminumMaterials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlAluminumMaterials()
    {
        return $this->hasMany(AlAluminumMaterials::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[AlPricingTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlPricingTables()
    {
        return $this->hasMany(AlPricingTable::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[System]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSystem()
    {
        return $this->hasOne(AlSystems::class, ['id' => 'system_id']);
    }

}
