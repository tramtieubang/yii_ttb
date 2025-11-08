<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_messages".
 *
 * @property int $id
 * @property int $room_id Phòng chat
 * @property int $sender_id Người gửi
 * @property string $message Nội dung tin nhắn
 * @property string|null $created_at Thời gian gửi
 *
 * @property ChatRooms $room
 * @property User $sender
 */
class ChatMessages extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id', 'sender_id'], 'required'],
            [['room_id', 'sender_id'], 'integer'],
            [['message'], 'string'],
            [['file_path', 'file_type', 'original_name'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatRooms::class, 'targetAttribute' => ['room_id' => 'id']],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sender_id' => 'id']],
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
            'sender_id' => 'Sender ID',
            'message' => 'Message',
            'created_at' => 'Created At',
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
     * Gets query for [[Sender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'sender_id']);
    }

}
