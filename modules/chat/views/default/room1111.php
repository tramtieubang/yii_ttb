<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\ChatRoom $room */
/** @var app\models\ChatMessage[] $messages */
?>

<h4>Phòng: <?= Html::encode($room->name) ?></h4>

<div id="chat-box" class="border rounded p-3 mb-3" style="height:400px; overflow:auto; background:#fafafa;">
  <?php foreach ($messages as $m): ?>
    <div class="<?= $m->sender_id == Yii::$app->user->id ? 'text-end' : 'text-start' ?>">
      <strong><?= Html::encode($m->sender->username ?? 'User '.$m->sender_id) ?>:</strong>
      <?= Html::encode($m->message) ?><br>
      <small class="text-muted"><?= $m->created_at ?></small>
    </div>
  <?php endforeach; ?>
</div>

<div class="d-flex">
  <input type="text" id="chat-message" class="form-control me-2" placeholder="Nhập tin nhắn...">
  <button class="btn btn-primary" id="send-btn">Gửi</button>
</div>

<?php
$sendUrl = Url::to(['chat/send']);
$fetchUrl = Url::to(['chat/fetch', 'room_id' => $room->id]);
$roomId = $room->id;
$js = <<<JS
function fetchMessages() {
  $.getJSON('$fetchUrl', function(data) {
    let html = '';
    data.forEach(m => {
      html += '<div class="' + (m.is_self ? 'text-end' : 'text-start') + '">';
      html += '<strong>' + m.sender + ':</strong> ' + m.message + '<br>';
      html += '<small class="text-muted">' + m.created_at + '</small>';
      html += '</div>';
    });
    $('#chat-box').html(html);
    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
  });
}

$('#send-btn').click(function() {
  let msg = $('#chat-message').val();
  if (!msg) return;
  $.post('$sendUrl', {room_id: $roomId, message: msg}, function(res) {
    if (res.success) {
      $('#chat-message').val('');
      fetchMessages();
    }
  });
});

setInterval(fetchMessages, 3000);
JS;
$this->registerJs($js);
?>
