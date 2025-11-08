<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_room_members".
 *
 * @property int $id
 * @property int $room_id Phòng chat
 * @property int $user_id Thành viên
 * @property string|null $joined_at Thời gian tham gia
 *
 * @property ChatRooms $room
 * @property User $user
 */
class ChatRoomMembers extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_room_members';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id', 'user_id'], 'required'],
            [['room_id', 'user_id'], 'integer'],
            [['joined_at'], 'safe'],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatRooms::class, 'targetAttribute' => ['room_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'Room ID',
            'user_id' => 'User ID',
            'joined_at' => 'Joined At',
        ];
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(ChatRooms::class, ['id' => 'room_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
