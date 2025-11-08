<?php
namespace app\modules\chat\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\ChatRooms;
use app\models\ChatMessages;
use app\models\User;

class ChatController extends Controller
{
    public $enableCsrfValidation = false;

    /* -----------------------------
     * GỬI TIN NHẮN
     * ----------------------------- */
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type       = Yii::$app->request->post('type'); // personal | group
        $roomId     = Yii::$app->request->post('room_id');
        $receiverId = Yii::$app->request->post('receiver_id');
        $message    = trim(Yii::$app->request->post('message'));
        $senderId   = Yii::$app->user->id;

        if (!$message) return ['success' => false, 'error' => 'Tin nhắn trống'];

        /* ---------- CÁ NHÂN ---------- */
        if ($type === 'personal') {
            if (!$receiverId && !$roomId)
                return ['success' => false, 'error' => 'Thiếu người nhận'];

            if (!$roomId) {
                // Tìm hoặc tạo phòng giữa 2 user
                $room = ChatRooms::find()
                    ->alias('r')
                    ->innerJoin('chat_room_members m1', 'm1.room_id = r.id')
                    ->innerJoin('chat_room_members m2', 'm2.room_id = r.id')
                    ->where(['r.is_group' => 0])
                    ->andWhere(['m1.user_id' => $senderId])
                    ->andWhere(['m2.user_id' => $receiverId])
                    ->one();

                if (!$room) {
                    $room = new ChatRooms([
                        'name' => 'Chat ' . $senderId . '-' . $receiverId,
                        'is_group' => 0,
                        'created_by' => $senderId
                    ]);
                    $room->save(false);

                    Yii::$app->db->createCommand()
                        ->batchInsert('chat_room_members', ['room_id', 'user_id'], [
                            [$room->id, $senderId],
                            [$room->id, $receiverId]
                        ])->execute();
                }
            } else {
                $room = ChatRooms::findOne($roomId);
            }
        }

        /* ---------- NHÓM ---------- */
        else {
            $room = ChatRooms::findOne(['id' => $roomId, 'is_group' => 1]);
            if (!$room) return ['success' => false, 'error' => 'Không tìm thấy nhóm'];
        }

        /* ---------- LƯU TIN ---------- */
        $msg = new ChatMessages([
            'room_id' => $room->id,
            'sender_id' => $senderId,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $msg->save(false);

        return ['success' => true, 'room_id' => $room->id];
    }

    /* -----------------------------
     * LẤY TIN NHẮN
     * ----------------------------- */
    public function actionFetch($room_id = null, $type = 'personal', $target_id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        if ($type === 'personal') {
            // Tìm phòng giữa 2 người
            $room = ChatRooms::find()
                ->alias('r')
                ->innerJoin('chat_room_members m1', 'm1.room_id = r.id')
                ->innerJoin('chat_room_members m2', 'm2.room_id = r.id')
                ->where(['r.is_group' => 0])
                ->andWhere(['m1.user_id' => $userId])
                ->andWhere(['m2.user_id' => $target_id])
                ->one();

            if (!$room) return []; // Chưa có phòng -> không có tin

            $room_id = $room->id;
        }

        // Lấy tin nhắn
        $messages = ChatMessages::find()
            ->where(['room_id' => $room_id])
            ->orderBy(['created_at' => SORT_ASC])
            ->asArray()
            ->all();

        foreach ($messages as &$m) {
            $sender = User::findOne($m['sender_id']);
            $m['sender'] = $sender ? $sender->username : 'Ẩn danh';
        }

        return $messages;
    }

    /* -----------------------------
     * KIỂM TRA TIN MỚI (poll)
     * ----------------------------- */
    public function actionPoll()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        $messages = ChatMessages::find()
            ->joinWith(['room.members', 'sender'])
            ->where(['chat_room_members.user_id' => $userId])
            ->andWhere(['chat_messages.is_read' => 0])
            ->andWhere(['<>', 'chat_messages.sender_id', $userId])
            ->orderBy(['chat_messages.created_at' => SORT_ASC])
            ->limit(10)
            ->all();

        $data = [];
        foreach ($messages as $m) {
            $data[] = [
                'room_id' => $m->room_id,
                'room_name' => $m->room->name,
                'is_group' => $m->room->is_group,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->username,
                'message' => $m->message
            ];
            $m->is_read = 1;
            $m->save(false);
        }

        return ['new_messages' => $data];
    }

    /* -----------------------------
     * ĐÁNH DẤU ĐÃ ĐỌC
     * ----------------------------- */
    public function actionMarkRead()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $roomId = Yii::$app->request->post('room_id');

        if (!$roomId) return ['success' => false];

        $msgs = ChatMessages::find()
            ->joinWith(['room.members'])
            ->where(['chat_room_members.user_id' => $userId])
            ->andWhere(['chat_messages.room_id' => $roomId])
            ->andWhere(['chat_messages.is_read' => 0])
            ->andWhere(['<>', 'chat_messages.sender_id', $userId])
            ->all();

        foreach ($msgs as $m) {
            $m->is_read = 1;
            $m->save(false);
        }

        return ['success' => true];
    }

    /* -----------------------------
     * ĐẾM SỐ TIN CHƯA ĐỌC
     * ----------------------------- */
    public function actionUnread()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        $messages = (new \yii\db\Query())
            ->select(['r.is_group', 'cm.sender_id', 'cm.room_id', 'COUNT(*) AS total'])
            ->from(['cm' => 'chat_messages'])
            ->innerJoin(['r' => 'chat_rooms'], 'r.id = cm.room_id')
            ->innerJoin(['m' => 'chat_room_members'], 'm.room_id = r.id')
            ->where(['m.user_id' => $userId, 'cm.is_read' => 0])
            ->andWhere(['<>', 'cm.sender_id', $userId])
            ->groupBy(['r.is_group', 'cm.sender_id', 'cm.room_id'])
            ->all();

        $counts = [];
        foreach ($messages as $row) {
            if ($row['is_group'])
                $counts['group_' . $row['room_id']] = (int)$row['total'];
            else
                $counts['personal_' . $row['sender_id']] = (int)$row['total'];
        }

        $total = array_sum($counts);
        return ['counts' => $counts, 'total' => $total];
    }
}
