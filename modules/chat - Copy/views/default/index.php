<?php
use yii\helpers\Html;

/* @var $users app\models\User[] */
/* @var $rooms app\models\ChatRooms[] */
/* @var $fetchUrl string */
/* @var $sendUrl string */
/* @var $pollUrl string */
/* @var $unreadUrl string */
/* @var $markReadUrl string */

$currentUserId = Yii::$app->user->id;
?>

<!-- Nút mở chat -->
<div id="chatOpenBtn" style="
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 65px;
    height: 65px;
    line-height: 65px;
    font-size: 30px;
    text-align: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    z-index: 9999;
">
    💬
    <span id="chatUnreadBadge" style="
        display:none; position:absolute; top:6px; right:8px;
        background:red; color:white; border-radius:50%;
        width:22px; height:22px; font-size:12px; line-height:22px;
        text-align:center; font-weight:bold;
        box-shadow:0 0 4px rgba(0,0,0,0.3);
    ">0</span>
</div>

<!-- Danh sách người dùng / nhóm -->
<div id="userListPopup" style="
    display:none; position:fixed; bottom:100px; right:25px;
    width:240px; max-height:350px; overflow-y:auto;
    background:#fff; border:1px solid #ccc;
    border-radius:10px; padding:10px; z-index:10000;
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
">
    <strong>Cá nhân:</strong>
    <?php foreach($users as $user): ?>
        <div class="userItem" data-type="personal" data-id="<?= $user->id ?>" data-name="<?= Html::encode($user->username) ?>" style="padding:5px; cursor:pointer;">
            👤 <?= Html::encode($user->username) ?>
            <span class="unread" id="unread_personal_<?= $user->id ?>" style="float:right; color:red; font-weight:bold;"></span>
        </div>
    <?php endforeach; ?>
    <hr>
    <strong>Nhóm:</strong>
    <?php foreach($rooms as $room): ?>
        <div class="userItem" data-type="group" data-id="<?= $room->id ?>" data-name="<?= Html::encode($room->name) ?>" style="padding:5px; cursor:pointer;">
            💬 <?= Html::encode($room->name) ?>
            <span class="unread" id="unread_group_<?= $room->id ?>" style="float:right; color:red; font-weight:bold;"></span>
        </div>
    <?php endforeach; ?>
</div>

<div id="chatContainer"></div>

<?php
\yii\web\JqueryAsset::register($this);
$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends'=>[\yii\web\JqueryAsset::class]]);
?>

<?php
$js = <<<JS
$(function(){
    var chatWindows = {};

    // === Mở danh sách ===
    $('#chatOpenBtn').click(() => $('#userListPopup').toggle());

    // === Hàm mở cửa sổ chat ===
    function openChatWindow(type, id, name, autoLoad=true) {
        const key = type + '_' + id;
        if (chatWindows[key]) {
            chatWindows[key].win.show();
            if(autoLoad) chatWindows[key].loadMessages();
            return;
        }

        const chatWin = \$(
            '<div id="chatWindow_' + key + '" style="\
                position:fixed; bottom:20px; right:' + (20 + Object.keys(chatWindows).length * 340) + 'px;\
                width:320px; height:420px; background:#fff;\
                border:1px solid #ccc; border-radius:10px;\
                display:flex; flex-direction:column; z-index:10001;\
                box-shadow:0 2px 10px rgba(0,0,0,0.3);">\
                <div class="header" style="background:#007bff;color:#fff;padding:10px;cursor:move;position:relative;font-weight:bold;">' 
                + name +
                '<span class="close" style="position:absolute;right:10px;top:5px;cursor:pointer;">✖</span>\
                </div>\
                <div class="messages" style="flex:1;padding:10px;overflow-y:auto;background:#f9f9f9;"></div>\
                <div class="inputDiv" style="display:flex;border-top:1px solid #ddd;">\
                    <input type="text" class="inputMsg" style="flex:1;padding:10px;border:none;" placeholder="Nhập tin nhắn...">\
                    <button class="sendBtn" style="padding:10px;background:#007bff;color:#fff;border:none;">Gửi</button>\
                </div>\
            </div>'
        );

        chatWin.data('type', type);
        chatWin.data('id', id);
        chatWin.data('room_id', (type === 'group') ? id : null);
        $('#chatContainer').append(chatWin);
        chatWin.draggable({ handle: '.header' });

        // Đóng cửa sổ
        chatWin.find('.close').click(function(){
            chatWin.remove();
            delete chatWindows[key];
        });

        // === Load tin nhắn ===
        function loadMessages(){
            const room_id = chatWin.data('room_id') || null;
            $.get('$fetchUrl', {room_id, type, target_id:id}, function(res){
                const msgsDiv = chatWin.find('.messages');
                msgsDiv.empty();

                if(res && res.length){
                    res.forEach(m=>{
                        const align = (m.sender_id == $currentUserId) ? 'right' : 'left';
                        const bg = (m.sender_id == $currentUserId) ? '#007bff' : '#eee';
                        const color = (m.sender_id == $currentUserId) ? 'white' : 'black';
                        msgsDiv.append('<div style="text-align:'+align+';margin:5px 0;">\
                            <span style="display:inline-block;padding:6px 10px;border-radius:10px;background:'+bg+';color:'+color+';">\
                            <b>' + m.sender + ':</b> ' + m.message + '</span></div>');
                    });
                } else {
                    msgsDiv.append('<div style="text-align:center;color:#aaa;">Chưa có tin nhắn nào.</div>');
                }
                msgsDiv.scrollTop(msgsDiv.prop('scrollHeight'));

                // Đánh dấu đã đọc
                $.post('$markReadUrl', { room_id: room_id, type: type, target_id: id });
                updateUnreadCount();
            });
        }

        // === Gửi tin nhắn ===
        chatWin.find('.sendBtn').click(function(){
            const msgInput = chatWin.find('.inputMsg');
            const msg = msgInput.val().trim();
            if(!msg) return;

            const data = {
                type: type,
                message: msg,
                room_id: chatWin.data('room_id'),
                receiver_id: (type === 'personal' ? id : null)
            };

            $.ajax({
                url: '$sendUrl',
                type: 'POST',
                data: data,
                headers: {'X-CSRF-Token': yii.getCsrfToken()},
                success: function(res){
                    if(res.success){
                        chatWin.data('room_id', res.room_id);
                        msgInput.val('');
                        loadMessages();
                    } else alert('Gửi lỗi! ' + res.error);
                }
            });
        });

        chatWin.find('.inputMsg').keypress(e=>{
            if(e.which===13){ e.preventDefault(); chatWin.find('.sendBtn').click(); }
        });

        chatWindows[key] = {win: chatWin, loadMessages};

        // Tải tin nhắn khi mở cửa sổ
        if (autoLoad) {
            setTimeout(() => loadMessages(), 200);
        }
    }

    // Mở từ danh sách
    $('#userListPopup').on('click', '.userItem', function(){
        openChatWindow($(this).data('type'), $(this).data('id'), $(this).data('name'), true);
        $('#userListPopup').hide();
    });

    // === Cập nhật số tin chưa đọc ===
    function updateUnreadCount(){
        $.get('$unreadUrl', function(res){
            let total = 0;
            $('.unread').text('');
            if(res && res.counts){
                Object.entries(res.counts).forEach(([key, val])=>{
                    $('#unread_'+key).text(val>0 ? '('+val+')' : '');
                    total += val;
                });
            }
            if(total>0){
                $('#chatUnreadBadge').text(total).show();
            } else {
                $('#chatUnreadBadge').hide();
            }
        });
    }

    // === Poll tin nhắn mới ===
    function pollMessages() {
        $.get('$pollUrl', function(res) {
            if(!res.new_messages) return;

            res.new_messages.forEach(m => {
                const key = (m.is_group ? 'group_' + m.room_id : 'personal_' + m.sender_id);
                const type = m.is_group ? 'group' : 'personal';
                const id   = m.is_group ? m.room_id : m.sender_id;
                const name = m.is_group ? m.room_name : m.sender_name;

                if (chatWindows[key]) {
                    chatWindows[key].loadMessages();
                } else {
                    openChatWindow(type, id, name, true);
                }
            });

            updateUnreadCount();
        });
    }

    // === Khởi động vòng lặp cập nhật ===
    setInterval(pollMessages, 5000);
    updateUnreadCount();
});
JS;

$this->registerJs($js);
?>
