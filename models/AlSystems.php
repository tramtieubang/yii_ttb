<?php

namespace app\models;

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
class AlSystems extends \yii\db\ActiveRecord
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
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'brand' => 'Brand',
            'origin' => 'Origin',
            'thickness' => 'Thickness',
            'color' => 'Color',
            'surface_type' => 'Surface Type',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
