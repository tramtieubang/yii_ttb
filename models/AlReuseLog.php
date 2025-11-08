<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_reuse_log".
 *
 * @property int $id ID bản ghi tái sử dụng
 * @property int $scrap_id FK -> al_scrap_aluminum
 * @property int $used_in_cut_group_id FK -> al_cut_groups
 * @property float $reuse_length Chiều dài nhôm tái sử dụng (mm)
 * @property int|null $quantity Số lượng nhôm tái sử dụng
 * @property string|null $note Ghi chú
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlScrapAluminum $scrap
 * @property AlCutGroups $usedInCutGroup
 */
class AlReuseLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_reuse_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['quantity'], 'default', 'value' => 1],
            [['scrap_id', 'used_in_cut_group_id', 'reuse_length'], 'required'],
            [['scrap_id', 'used_in_cut_group_id', 'quantity'], 'integer'],
            [['reuse_length'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['note'], 'string', 'max' => 255],
            [['used_in_cut_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlCutGroups::class, 'targetAttribute' => ['used_in_cut_group_id' => 'id']],
            [['scrap_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlScrapAluminum::class, 'targetAttribute' => ['scrap_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scrap_id' => 'Scrap ID',
            'used_in_cut_group_id' => 'Used In Cut Group ID',
            'reuse_length' => 'Reuse Length',
            'quantity' => 'Quantity',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Scrap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScrap()
    {
        return $this->hasOne(AlScrapAluminum::class, ['id' => 'scrap_id']);
    }

    /**
     * Gets query for [[UsedInCutGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsedInCutGroup()
    {
        return $this->hasOne(AlCutGroups::class, ['id' => 'used_in_cut_group_id']);
    }

}
