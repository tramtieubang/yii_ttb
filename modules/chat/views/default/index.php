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

<!-- 🔵 Nút mở chat -->
<div id="chatOpenBtn" style="
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 55px;
    height: 55px;
    line-height: 55px;
    font-size: 30px;
    text-align: center;
    border-radius: 50%;
    background: linear-gradient(95deg, #007bff, #0056b3);
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

<!-- 🔵 Danh sách người dùng / nhóm -->
<div id="userListPopup" style="
    display:none; position:fixed; bottom:80px; right:25px;
    width:260px; max-height:350px; overflow-y:auto;
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

    // === Toggle danh sách ===
    $('#chatOpenBtn').click(() => $('#userListPopup').toggle());

    // === Mở cửa sổ chat ===
    function openChatWindow(type, id, name, autoLoad=true) {
        const key = type + '_' + id;
        if (chatWindows[key]) {
            chatWindows[key].win.show();
            if(autoLoad) chatWindows[key].loadMessages();
            return;
        }

        const chatWin = $('<div id="chatWindow_' + key + '" style="\
            position:fixed; bottom:20px; right:' + (20 + Object.keys(chatWindows).length * 340) + 'px;\
            width:320px; height:440px; background:#fff;\
            border:1px solid #ccc; border-radius:10px;\
            display:flex; flex-direction:column; z-index:10001;\
            box-shadow:0 2px 10px rgba(0,0,0,0.3);">\
            <div class="header" style="background:#007bff;color:#fff;padding:10px;cursor:move;position:relative;font-weight:bold;">' 
            + name +
            '<span class="close" style="position:absolute;right:10px;top:5px;cursor:pointer;">✖</span>\
            </div>\
            <div class="messages" style="flex:1;padding:10px;overflow-y:auto;background:#f9f9f9;"></div>\
            <div class="inputDiv" style="display:flex;border-top:1px solid #ddd;">\
                <input type="file" class="fileInput" style="display:none;">\
                <button class="fileBtn" style="padding:10px;background:#6c757d;color:white;border:none;">📎</button>\
                <input type="text" class="inputMsg" style="flex:1;padding:10px;border:none;" placeholder="Nhập tin nhắn...">\
                <button class="sendBtn" style="padding:10px;background:#007bff;color:#fff;border:none;">Gửi</button>\
            </div>\
        </div>');

        chatWin.data('type', type);
        chatWin.data('id', id);
       // chatWin.data('room_id', null); // ban đầu null
       chatWin.data('room_id', (type==='group') ? id : null);

        $('#chatContainer').append(chatWin);
        chatWin.draggable({ handle: '.header' });

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
                        const bg = (m.sender_id == $currentUserId) ? '#dceffa' : '#eee';
                        const color = (m.sender_id == $currentUserId) ? '#3c4042ff' : '#3c4042ff';
                        let content = m.message || '';

                        const isMe = (m.sender_id == $currentUserId);
                       
                        // chỉ hiện tên nếu không phải chính user
                        const senderName = isMe ? '' : (m.sender+': ' || 'Ẩn danh');

                        if (m.file_path) {
                            if (m.file_type && m.file_type.startsWith('image')) {
                                content += '<br><img src="'+m.file_path+'" class="chat-image" style="max-width:150px;border-radius:8px;cursor:pointer;margin-top:5px;">';
                                content += ' <a href="'+m.file_path+'" download title="Tải về" style="margin-left:5px;color:#007bff;text-decoration:none;"> ⬇️ </a>';
                            } else {
                                const fileName = m.file_path.split('/').pop();
                                content += '<br><span style="color:#333;">📎 '+fileName+'</span>';
                                content += ' <a href="'+m.file_path+'" download title="Tải về" style="margin-left:5px;color:#007bff;text-decoration:none;">⬇️ Lưu</a>';
                            }
                        }

                        msgsDiv.append('<div style="text-align:'+align+';margin:5px 0;">\
                            <span style="display:inline-block;padding:6px 10px;border-radius:5px;background:'+bg+';color:'+color+';">\
                            <b>' + senderName + '</b> ' + content + '</span></div>');
                    });
                } else {
                    msgsDiv.append('<div style="text-align:center;color:#aaa;">Chưa có tin nhắn nào.</div>');
                }

                msgsDiv.scrollTop(msgsDiv.prop('scrollHeight'));
                if(room_id) $.post('$markReadUrl', { room_id: room_id, type: type, target_id: id });
                updateUnreadCount();

                chatWin.find('.chat-image').off('click').on('click', function(){
                    const imgSrc = $(this).attr('src');
                    $('<div><img src="'+imgSrc+'" style="max-width:90vw;max-height:90vh;"></div>').dialog({
                        modal: true,
                        title: 'Xem ảnh',
                        width: 'auto',
                        height: 'auto',
                        resizable: false
                    });
                });
            });
        }

        // === Gửi tin nhắn hoặc file ===
        function sendMessage(message, fileData=null) {
            const fd = new FormData();
            fd.append('type', type);
            fd.append('message', message);
            fd.append('receiver_id', (type==='personal') ? id : '');
            fd.append('room_id', chatWin.data('room_id') || ''); // backend sẽ tạo phòng nếu null

            if(fileData) fd.append('file', fileData);

            if (fileData) {
                const maxSize = 10*1024*1024;
                const allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','zip','mp4','mp3'];
                const ext = fileData.name.split('.').pop().toLowerCase();
                if (!allowed.includes(ext)) { alert('Không được gửi loại file .' + ext); return; }
                if (fileData.size > maxSize) { alert('File quá lớn! Giới hạn 10MB.'); return; }
            }

            $.ajax({
                url: '$sendUrl',
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-Token': yii.getCsrfToken()},
                success: function(res){
                    if(res && res.success){
                        chatWin.data('room_id', res.room_id); // 🔹 bắt buộc cập nhật room_id
                        chatWin.find('.inputMsg').val('');
                        loadMessages();
                    } else {
                        let err = res && res.error ? res.error : 'Lỗi không xác định';
                        if (typeof err === 'object') err = JSON.stringify(err);
                        alert('Gửi lỗi! ' + err);
                        console.error('Send fail', res);
                    }
                },
                error: function(xhr){
                    let msg = 'Lỗi server (' + xhr.status + '): ';
                    try { const j = JSON.parse(xhr.responseText); msg += j.error || JSON.stringify(j); }
                    catch(e) { msg += xhr.responseText; }
                    alert(msg);
                    console.error('Upload error', xhr);
                }
            });
        }

        chatWin.find('.sendBtn').click(function(){
            const msg = chatWin.find('.inputMsg').val().trim();
            if(!msg) return;
            sendMessage(msg);
        });

        chatWin.find('.fileBtn').click(()=>chatWin.find('.fileInput').click());
        chatWin.find('.fileInput').on('change', function(){
            const file = this.files[0];
            if(file) sendMessage('', file); // 🔹 gửi file ngay cả khi chưa có room_id
            $(this).val('');
        });

        chatWin.find('.inputMsg').keypress(e=>{
            if(e.which===13){ e.preventDefault(); chatWin.find('.sendBtn').click(); }
        });

        chatWindows[key] = {win: chatWin, loadMessages};
        if(autoLoad) setTimeout(()=>loadMessages(),200);
    }

    // Mở từ danh sách người dùng / nhóm
    $('#userListPopup').on('click','.userItem', function(){
        openChatWindow($(this).data('type'), $(this).data('id'), $(this).data('name'), true);
        $('#userListPopup').hide();
    });

    // === Đếm tin chưa đọc ===
    function updateUnreadCount(){
        $.get('$unreadUrl', function(res){
            let total = 0;
            $('.unread').text('');
            if(res && res.counts){
                Object.entries(res.counts).forEach(([key,val])=>{
                    $('#unread_'+key).text(val>0 ? '('+val+')':'');
                    total += val;
                });
            }
            if(total>0) $('#chatUnreadBadge').text(total).show();
            else $('#chatUnreadBadge').hide();
        });
    }

    // === Poll tin mới ===
    function pollMessages(){
        $.get('$pollUrl', function(res){
            if(!res.new_messages) return;
            res.new_messages.forEach(m=>{
                const key = (m.is_group ? 'group_'+m.room_id : 'personal_'+m.sender_id);
                const type = m.is_group ? 'group' : 'personal';
                const id = m.is_group ? m.room_id : m.sender_id;
                const name = m.is_group ? m.room_name : m.sender_name;
                if(chatWindows[key]) chatWindows[key].loadMessages();
                else openChatWindow(type,id,name,true);
            });
            updateUnreadCount();
        });
    }

    setInterval(pollMessages, 5000);
    updateUnreadCount();
});

JS;

$this->registerJs($js);
?>
