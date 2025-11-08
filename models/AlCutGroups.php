<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "al_cut_groups".
 *
 * @property int $id ID nhóm cắt
 * @property int $order_id FK -> al_orders
 * @property int $material_id FK -> al_aluminum_materials
 * @property int $cut_length Chiều dài cần cắt (mm)
 * @property int $quantity Số lượng cần cắt
 * @property int|null $waste_length Chiều dài hao hụt (mm)
 * @property int|null $total_used_length Tổng chiều dài đã dùng (mm)
 * @property int|null $used_from_scrap_id Nếu dùng nhôm dư (al_scrap_aluminum) để cắt, lưu ID nhôm dư này; nullable
 * @property string|null $note Ghi chú
 * @property string|null $created_at Ngày tạo
 * @property string|null $updated_at Ngày cập nhật
 *
 * @property AlReuseLog[] $alReuseLogs
 * @property AlScrapAluminum[] $alScrapAluminums
 * @property AlAluminumMaterials $material
 * @property AlOrders $order
 */
class AlCutGroups extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'al_cut_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['used_from_scrap_id', 'note'], 'default', 'value' => null],
            [['quantity'], 'default', 'value' => 1],
            [['total_used_length'], 'default', 'value' => 0],
            [['order_id', 'material_id', 'cut_length'], 'required'],
            [['order_id', 'material_id', 'cut_length', 'quantity', 'waste_length', 'total_used_length', 'used_from_scrap_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['note'], 'string', 'max' => 255],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlAluminumMaterials::class, 'targetAttribute' => ['material_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlOrders::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'material_id' => 'Material ID',
            'cut_length' => 'Cut Length',
            'quantity' => 'Quantity',
            'waste_length' => 'Waste Length',
            'total_used_length' => 'Total Used Length',
            'used_from_scrap_id' => 'Used From Scrap ID',
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
        return $this->hasMany(AlReuseLog::class, ['used_in_cut_group_id' => 'id']);
    }

    /**
     * Gets query for [[AlScrapAluminums]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlScrapAluminums()
    {
        return $this->hasMany(AlScrapAluminum::class, ['cut_group_id' => 'id']);
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

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(AlOrders::class, ['id' => 'order_id']);
    }

}
