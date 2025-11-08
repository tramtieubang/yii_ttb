<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_aluminum_materials".
 *
 * @property int $id
 * @property int $profile_id ID profile nhôm
 * @property string $code Mã vật liệu
 * @property string $name Tên vật liệu nhôm
 * @property int $length Chiều dài (mm)
 * @property int $stock_quantity Số lượng tồn kho
 * @property int $stock_length Chiều dài tồn kho (mm)
 * @property float $unit_price Đơn giá
 * @property string|null $note Ghi chú
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AlCutGroups[] $alCutGroups
 * @property AlOrderDetails[] $alOrderDetails
 * @property AlScrapAluminum[] $alScrapAluminums
 * @property AlProfiles $profile
 */
class AlAluminumMaterials extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_aluminum_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['stock_length'], 'default', 'value' => 0],
            [['unit_price'], 'default', 'value' => 0.00],
            [['profile_id', 'code', 'name', 'length', 'created_at', 'updated_at'], 'required'],
            [['profile_id', 'length', 'stock_quantity', 'stock_length'], 'integer'],
            [['unit_price'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
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
            'code' => 'Code',
            'name' => 'Name',
            'length' => 'Length',
            'stock_quantity' => 'Stock Quantity',
            'stock_length' => 'Stock Length',
            'unit_price' => 'Unit Price',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AlCutGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlCutGroups()
    {
        return $this->hasMany(AlCutGroups::class, ['material_id' => 'id']);
    }

    /**
     * Gets query for [[AlOrderDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlOrderDetails()
    {
        return $this->hasMany(AlOrderDetails::class, ['material_id' => 'id']);
    }

    /**
     * Gets query for [[AlScrapAluminums]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlScrapAluminums()
    {
        return $this->hasMany(AlScrapAluminum::class, ['material_id' => 'id']);
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
