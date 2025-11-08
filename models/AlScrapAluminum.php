<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_scrap_aluminum".
 *
 * @property int $id ID chính
 * @property int $cut_group_id ID nhóm cắt
 * @property int $material_id ID vật liệu nhôm (nguồn tạo ra nhôm vụn)
 * @property int $remaining_length Chiều dài còn lại sau khi cắt (mm)
 * @property float $weight Trọng lượng nhôm vụn (kg)
 * @property int|null $is_reused Đã tái sử dụng chưa (0=chưa,1=đã)
 * @property string|null $note Ghi chú
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlReuseLog[] $alReuseLogs
 * @property AlCutGroups $cutGroup
 * @property AlAluminumMaterials $material
 */
class AlScrapAluminum extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_scrap_aluminum';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['is_reused'], 'default', 'value' => 0],
            [['cut_group_id', 'material_id', 'remaining_length', 'weight'], 'required'],
            [['cut_group_id', 'material_id', 'remaining_length', 'is_reused'], 'integer'],
            [['weight'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['note'], 'string', 'max' => 255],
            [['cut_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlCutGroups::class, 'targetAttribute' => ['cut_group_id' => 'id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlAluminumMaterials::class, 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cut_group_id' => 'Cut Group ID',
            'material_id' => 'Material ID',
            'remaining_length' => 'Remaining Length',
            'weight' => 'Weight',
            'is_reused' => 'Is Reused',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AlReuseLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlReuseLogs()
    {
        return $this->hasMany(AlReuseLog::class, ['scrap_id' => 'id']);
    }

    /**
     * Gets query for [[CutGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCutGroup()
    {
        return $this->hasOne(AlCutGroups::class, ['id' => 'cut_group_id']);
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

}
