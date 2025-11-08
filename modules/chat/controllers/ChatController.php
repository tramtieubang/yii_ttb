<?php
namespace app\modules\chat\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use app\models\ChatRooms;
use app\models\ChatMessages;
use app\models\User;

class ChatController extends Controller
{
    public $enableCsrfValidation = false;

   /* ==========================
     * GỬI TIN NHẮN
     * ========================== */
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type       = Yii::$app->request->post('type'); // personal | group
        $roomId     = Yii::$app->request->post('room_id');
        $receiverId = Yii::$app->request->post('receiver_id');
        $message    = trim(Yii::$app->request->post('message', ''));
        $senderId   = Yii::$app->user->id;

        $file       = UploadedFile::getInstanceByName('file');
        $filePath   = null;
        $fileType   = null;
        $originalName = null;

        // ====== XỬ LÝ FILE ======
        if ($file) {
            $uploadDir = Yii::getAlias('@webroot/uploads/chat/');
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $file->name);
            $filePath = '/uploads/chat/' . $safeName;
            $originalName = $file->name;

            if (!$file->saveAs($uploadDir . $safeName)) {
                return ['success'=>false, 'error'=>'Lỗi lưu file'];
            }

            $ext = strtolower($file->extension);
            if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) $fileType = 'image';
            elseif (in_array($ext, ['mp4','avi','mov','mkv'])) $fileType = 'video';
            else $fileType = 'file';
        }

        if (!$message && !$filePath) {
            return ['success'=>false,'error'=>'Tin nhắn trống'];
        }

        // ====== LẤY HOẶC TẠO PHÒNG CHAT ======
        if ($type === 'personal') {
            if (!$roomId) {
                if (!$receiverId) {
                    return ['success'=>false,'error'=>'Thiếu người nhận'];
                }

                // Kiểm tra phòng chat cá nhân đã tồn tại chưa
                $room = ChatRooms::find()
                    ->alias('r')
                    ->innerJoin('chat_room_members m1','m1.room_id=r.id')
                    ->innerJoin('chat_room_members m2','m2.room_id=r.id')
                    ->where(['r.is_group'=>0])
                    ->andWhere(['m1.user_id'=>$senderId])
                    ->andWhere(['m2.user_id'=>$receiverId])
                    ->one();

                if (!$room) {
                    // Tạo mới phòng chat
                    $room = new ChatRooms([
                        'name' => 'Chat '.min($senderId,$receiverId).'-'.max($senderId,$receiverId),
                        'is_group' => 0,
                        'created_by' => $senderId,
                    ]);

                    if (!$room->save()) {
                        return [
                            'success'=>false,
                            'error'=>'Không tạo được phòng chat',
                            'details'=>$room->getErrors()
                        ];
                    }

                    Yii::$app->db->createCommand()->batchInsert('chat_room_members',['room_id','user_id'],[
                        [$room->id,$senderId],
                        [$room->id,$receiverId],
                    ])->execute();
                }
            } else {
                $room = ChatRooms::findOne($roomId);
                if (!$room) {
                    // Nếu roomId có nhưng không tồn tại, tạo mới phòng cho cá nhân
                    if (!$receiverId) return ['success'=>false,'error'=>'Thiếu người nhận'];
                    $room = new ChatRooms([
                        'name' => 'Chat '.min($senderId,$receiverId).'-'.max($senderId,$receiverId),
                        'is_group' => 0,
                        'created_by' => $senderId,
                    ]);
                    if (!$room->save()) {
                        return ['success'=>false,'error'=>'Không tạo được phòng chat','details'=>$room->getErrors()];
                    }
                    Yii::$app->db->createCommand()->batchInsert('chat_room_members',['room_id','user_id'],[
                        [$room->id,$senderId],
                        [$room->id,$receiverId],
                    ])->execute();
                }
            }
        } else {
            $room = ChatRooms::findOne(['id'=>$roomId,'is_group'=>1]);
            if (!$room) return ['success'=>false,'error'=>'Nhóm không tồn tại'];
        }

        // ====== LƯU TIN NHẮN ======
        $msg = new ChatMessages([
            'room_id'      => $room->id,
            'sender_id'    => $senderId,
            'message'      => $message ?: '',
            'file_path'    => $filePath,
            'file_type'    => $fileType,
            'original_name'=> $originalName,
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        if (!$msg->save()) {
            return ['success'=>false,'error'=>'Không lưu được tin nhắn','details'=>$msg->getErrors()];
        }

        return [
            'success'=>true,
            'room_id'=>$room->id,
            'file_path'=>$filePath,
            'file_type'=>$fileType,
            'original_name'=>$originalName,
            'message'=>$message,
        ];
    }

    // ==========================
    // Các action fetch, poll, markRead, unread giữ nguyên
    // ==========================
    public function actionFetch($room_id=null,$type='personal',$target_id=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;

        if ($type==='personal' && !$room_id && $target_id) {
            $room = ChatRooms::find()
                ->alias('r')
                ->innerJoin('chat_room_members m1', 'm1.room_id=r.id')
                ->innerJoin('chat_room_members m2', 'm2.room_id=r.id')
                ->where(['r.is_group'=>0])
                ->andWhere(['m1.user_id'=>$userId])
                ->andWhere(['m2.user_id'=>$target_id])
                ->one();
            if (!$room) return [];
            $room_id = $room->id;
        }

        $messages = ChatMessages::find()->where(['room_id'=>$room_id])->orderBy(['created_at'=>SORT_ASC])->asArray()->all();
        foreach($messages as &$m){
            $sender = User::findOne($m['sender_id']);
            $m['sender'] = $sender ? $sender->username : 'Ẩn danh';
        }
        return $messages;
    }

    /* ============================================================
     * POLLING: KIỂM TRA TIN MỚI
     * ============================================================ */
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
                'message' => $m->message,
                'file_path' => $m->file_path,
                'file_type' => $m->file_type,
                'original_name' => $m->original_name,
            ];
            $m->is_read = 1;
            $m->save(false);
        }

        return ['new_messages' => $data];
    }

    /* ============================================================
     * ĐÁNH DẤU TIN ĐÃ ĐỌC
     * ============================================================ */
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

    /* ============================================================
     * ĐẾM TIN CHƯA ĐỌC
     * ============================================================ */
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
