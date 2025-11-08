<?php

namespace app\modules\alsystems\models;

use app\models\AlProfiles;
use app\models\AlSystems;
use Yii;

/**
 * This is the model class for table "al_systems".
 *
 * @property int $id ID chính
 * @property string $code Mã hệ nhôm (VD: XF55, PMA60, TP50)
 * @property string $name Tên hệ nhôm (VD: Xingfa 55, PMA 60, Topal 50...)
 * @property string|null $brand Thương hiệu nhôm (VD: Xingfa, PMA, Topal...)
 * @property string|null $origin Xuất xứ / Nước sản xuất (VD: Trung Quốc, Việt Nam, Malaysia)
 * @property float|null $thickness Độ dày trung bình (mm)
 * @property string|null $color Màu sắc tiêu chuẩn (VD: Ghi, Trắng sứ, Đen, Nâu cà phê)
 * @property string|null $surface_type Loại bề mặt (VD: sơn tĩnh điện, anod, phủ bóng)
 * @property string|null $description Mô tả chi tiết hệ nhôm
 * @property string|null $status Trạng thái: active/inactive
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlProfiles[] $alProfiles
 */
class AlSystemsForm extends AlSystems
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_systems';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand', 'origin', 'thickness', 'color', 'surface_type', 'description'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'active'],
            [['code', 'name'], 'required'],
            [['thickness'], 'number'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['brand', 'origin', 'color', 'surface_type'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 20],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Mã số',
            'code' => 'Mã hệ nhôm',
            'name' => 'Tên hệ nhôm',
            'brand' => 'Thương hiệu',
            'origin' => 'Xuất xứ',
            'thickness' => 'Độ dày (mm)',
            'color' => 'Màu sắc',
            'surface_type' => 'Loại bề mặt',
            'description' => 'Mô tả',
            'status' => 'Trạng thái',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];

    }

    /**
     * Gets query for [[AlProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlProfiles()
    {
        return $this->hasMany(AlProfiles::class, ['system_id' => 'id']);
    }

}
