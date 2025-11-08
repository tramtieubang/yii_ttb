<?php
namespace app\modules\chat\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use app\models\ChatRooms;
use app\models\User;
use Yii;

class ChatPopup extends Widget
{

    public function run()
    {
        $currentUserId = Yii::$app->user->id;

        $users = \app\models\User::find()
            ->where(['<>', 'id', $currentUserId])
            ->all();

        $rooms = ChatRooms::find()
            ->joinWith('members')
            ->where(['chat_room_members.user_id' => $currentUserId])
            ->andWhere(['chat_rooms.is_group' => 1])
            ->all();

        // URL AJAX
        $fetchUrl    = Url::to(['/chat/chat/fetch']);
        $sendUrl     = Url::to(['/chat/chat/send']);
        $findRoomUrl = Url::to(['/chat/chat/find-personal-room']);
        $pollUrl     = Url::to(['/chat/chat/poll']);
        $unreadUrl   = Url::to(['/chat/chat/unread']); // 🟢 thêm dòng này
        $markReadUrl = Url::to(['/chat/chat/mark-read']);

        return $this->render('@app/modules/chat/views/default/index', [
            'rooms'       => $rooms,
            'users'       => $users,
            'fetchUrl'    => $fetchUrl,
            'sendUrl'     => $sendUrl,
            'findRoomUrl' => $findRoomUrl,
            'pollUrl'     => $pollUrl,
            'unreadUrl'   => $unreadUrl, // 🟢 truyền vào view
            'markReadUrl' => $markReadUrl,
        ]);
    }

}
