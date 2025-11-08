<?php
namespace app\modules\chat\controllers;

use Yii;
use yii\web\Controller;
use app\models\ChatMessages;
use app\models\ChatRooms;
use yii\filters\VerbFilter;
use yii\web\Response;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'bulkdelete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex($room_id = null)
    {       
       /* //echo Yii::getAlias('@app/modules/chat/views/default/index.php');
        // Ví dụ: lấy phòng chat mặc định hoặc phòng đầu tiên
        $currentRoom = ChatRooms::find()->orderBy(['id' => SORT_ASC])->one();

        // Nếu có model ChatRoom, thì có thể load thêm messages
        // Giả sử bạn đã định nghĩa quan hệ getMessages() trong model ChatRoom

        return $this->render('index', [
            'currentRoom' => $currentRoom,
        ]); */

        $userId = Yii::$app->user->id;

        $rooms = ChatRooms::find()
            ->joinWith('members')
            ->where([
                        'chat_room_members.user_id' => $userId,
                        'chat_rooms.is_group' => 1
                    ])
            ->all();

        return $this->render('index', [
            'rooms' => $rooms,
        ]);

    }

    public function actionSendMessage($room_id)
    {
        /* $message = new ChatMessages([
            'room_id' => Yii::$app->request->post('room_id'),
            'sender_id' => Yii::$app->user->id,
            'message' => Yii::$app->request->post('message'),
            'created_at' => time(),
        ]);
        $message->save(false);
        return $this->asJson(['success' => true]); */

        Yii::$app->response->format = Response::FORMAT_JSON;
        $messages = ChatMessages::find()
            ->where(['room_id' => $room_id])
            ->with('sender')
            ->orderBy(['created_at' => SORT_ASC])
            ->asArray()
            ->all();
        return $messages;
    }

    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ChatMessages();
        $model->room_id = Yii::$app->request->post('room_id');
        $model->sender_id = Yii::$app->user->id;
        $model->message = Yii::$app->request->post('message');

        if ($model->save()) {
            return ['success' => true];
        }

        return ['success' => false, 'errors' => $model->errors];
    }

}
