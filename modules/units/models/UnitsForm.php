<?php

namespace app\modules\units\models;

use app\models\Units;
use Yii;

/**
 * This is the model class for table "units".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class UnitsForm extends Units
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'units';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['code', 'name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 100],
            [['note'], 'string', 'max' => 255],
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
            'code' => 'Mã',
            'name' => 'Tên',
            'note' => 'Ghi chú',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];
    }

}
