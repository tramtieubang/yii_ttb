<?php

namespace app\modules\alprofiles\models;

use app\models\AlAluminumMaterials;
use app\models\AlPricingTable;
use app\models\AlProfiles;
use app\models\AlSystems;
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
class AlProfilesForm extends AlProfiles
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
            [['door_types', 'image_url', 'note'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'active'],

            // 👇 Thêm hai dòng default này để cho phép null:
            [['length','weight_per_meter', 'unit_price'], 'default', 'value' => null],

            [['system_id', 'code', 'name'], 'required'],
            [['system_id'], 'integer'],
           
            [['length'], 'filter', 'filter' => function($value) {
                $value = str_replace(['.', ' mm'], '', $value); // bỏ dấu chấm và ' mm'
                return trim($value);
            }],
            [['length'], 'number'],

            [['weight_per_meter'], 'filter', 'filter' => function($value) {
                $value = str_replace(['.', ' gm'], '', $value); // bỏ dấu chấm và ' gm'
                return trim($value);
            }],
            [['weight_per_meter'], 'number'],

            [['unit_price'], 'filter', 'filter' => function($value) {
                $value = str_replace(['.', ' VNĐ'], '', $value); // bỏ dấu chấm và ' VNĐ'
                return trim($value);
            }],
            [['unit_price'], 'number'],


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
            'system_id' => 'Mã hệ nhôm',
            'code' => 'Mã thanh nhôm',
            'name' => 'Tên thanh nhôm',
            'door_types' => 'Loại cửa áp dụng',
            'length' => 'Chiều dài (mm)',
            'weight_per_meter' => 'Trọng lượng / mét (g/m)',
            'unit_price' => 'Đơn giá / mét (VNĐ/m)',
            'image_url' => 'Hình minh họa',
            'note' => 'Ghi chú',
            'status' => 'Trạng thái',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
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
