<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_rooms".
 *
 * @property int $id
 * @property string $name Tên phòng hoặc người chat
 * @property int|null $is_group Có phải nhóm hay không
 * @property int $created_by Người tạo phòng
 * @property string|null $created_at Thời gian tạo
 *
 * @property ChatMessages[] $chatMessages
 * @property ChatRoomMembers[] $chatRoomMembers
 * @property User $createdBy
 */
class ChatRooms extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_rooms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_group'], 'default', 'value' => 0],
            [['name', 'created_by'], 'required'],
            [['is_group', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_group' => 'Is Group',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ChatMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(ChatMessages::class, ['room_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * Gets query for [[ChatRoomMembers]].
     *
     * @return \yii\db\ActiveQuery
     */
   /*  public function getMembers()
    {
        return $this->hasMany(ChatRoomMembers::class, ['room_id' => 'id']);
    } */

   // Quan hệ members
    public function getMembers()
    {
        // 'chat_room_members' là bảng trung gian: room_id, user_id
        return $this->hasMany(User::class, ['id' => 'user_id'])
                    ->viaTable('chat_room_members', ['room_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
   
    public function getSender()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'sender_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(ChatRooms::class, ['id' => 'room_id']);
    }

}
