<?php

use app\custom\PermissionHelper;
use yii\helpers\Html;

?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* --- Tổng thể --- */
body {
    background-color: #f4f6f8;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

/* --- Dashboard wrapper --- */
.home-dashboard {
    max-width: 1200px;
    margin: 40px auto;
    padding: 10px;
}
.home-dashboard h4 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 22px;
    color: #333;
    font-weight: 600;
}

/* --- Panel nhóm menu --- */
.home-panel {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    padding: 20px 25px;
}
.home-panel h5 {
    font-size: 18px;
    font-weight: 600;
    color: #444;
    margin-bottom: 20px;
    border-left: 5px solid #007bff;
    padding-left: 10px;
}

/* --- Lưới menu --- */
.home-menu-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);  /* 4 cột mỗi hàng */
    gap: 20px;
    align-items: stretch;
}

/* --- Ô menu --- */
.home-menu-item {
    background: #fff;
    border-radius: 12px;
    text-align: center;
    padding: 25px 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #333;
    border: 2px solid transparent;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.home-menu-item i {
    font-size: 34px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}
.home-menu-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

/* --- Hiệu ứng hover riêng cho từng nhóm --- */
.home-menu-admin:hover {
    border-color: #007bff;
    color: #007bff;
}
.home-menu-category:hover {
    border-color: #28a745;
    color: #28a745;
}
.home-menu-product:hover {
    border-color: #ff9800;
    color: #ff9800;
}
.home-menu-invoice:hover {
    border-color: #d10f08;
    color: #d10f08;
}
.home-menu-aluminum:hover {
    border-color: #3A6073;
    color: #3A6073;
}

/* --- Khi hover icon và text đổi cùng màu --- */
.home-menu-item:hover i,
.home-menu-item:hover .home-menu-label {
    color: inherit;
}

/* --- Tên menu --- */
.home-menu-label {
    font-size: 15px;
    font-weight: 600;
    transition: color 0.3s ease;
}

</style>

<div class="home-dashboard">
    <h4>Xin chào, <?= Html::encode(Yii::$app->user->identity->username) ?> 👋</h4>

    <!-- SUPER ADMIN -->
     <?php if (
        PermissionHelper::check('user_management/user/default/index') ||
        PermissionHelper::check('user_management/role/default/index') ||
        PermissionHelper::check('user_management/permission/default/index') ||
        PermissionHelper::check('user_management/permission_group/default/index') ||
        PermissionHelper::check('user_management/session_manager/default/index') 
        ): 
    ?>
        <div class="home-panel">
            <h5><i class="fas fa-crown text-primary"></i> Hệ thống</h5>
            <div class="home-menu-grid">
                <?php if (PermissionHelper::check('/user_management/user/default')): ?>
                    <?= Html::a('<i class="fas fa-users-cog"></i><div class="home-menu-label">Quản lý Người dùng</div>', ['/user_management/user/default'], ['class' => 'home-menu-item home-menu-admin']) ?>
                <?php endif; ?>
                <?php if (PermissionHelper::check('/user_management/role/default')): ?>
                    <?= Html::a('<i class="fe fe-users"></i><div class="home-menu-label">Vai trò</div>', ['/user_management/role/default'], ['class' => 'home-menu-item home-menu-admin']) ?>
                <?php endif; ?>
                <?php if (PermissionHelper::check('/user_management/permission/default')): ?>
                    <?= Html::a('<i class="fas fa-user-shield"></i><div class="home-menu-label">Quyền hạn</div>', ['/user_management/permission/default'], ['class' => 'home-menu-item home-menu-admin']) ?>
                <?php endif; ?>
                <?php if (PermissionHelper::check('/user_management/permission_group/default')): ?>
                    <?= Html::a('<i class="fe fe-layers"></i><div class="home-menu-label">Nhóm quyền</div>', ['/user_management/permission_group/default'], ['class' => 'home-menu-item home-menu-admin']) ?>
                <?php endif; ?>
                 <?php if (PermissionHelper::check('/user_management/session_manager/default')): ?>
                    <?= Html::a('<i class="fe fe-lock"></i><div class="home-menu-label">Nhóm quyền</div>', ['/user_management/session_manager/default'], ['class' => 'home-menu-item home-menu-admin']) ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- CATEGORY -->
    <?php if (
        PermissionHelper::check('/categories/default/index') ||
        PermissionHelper::check('/customers/default/index') ||
        PermissionHelper::check('/units/default/index') 
    ): ?>
        <div class="home-panel">
            <h5><i class="fas fa-chalkboard-teacher text-success"></i> Danh mục</h5>
            <div class="home-menu-grid">
                <?php if (PermissionHelper::check('/categories/default')): ?>
                    <?= Html::a('<i class="fe fe-grid"></i><div class="home-menu-label">Danh mục sản phẩm</div>', ['/categories/default'], ['class' => 'home-menu-item home-menu-category']) ?>
                <?php endif; ?>
                <?php if (PermissionHelper::check('/customers/default/index')): ?>
                    <?= Html::a('<i class="fe fe-users"></i><div class="home-menu-label">Danh mục khách hàng</div>', ['/customers/default/index'], ['class' => 'home-menu-item home-menu-category']) ?>
                <?php endif; ?>
                <?php if (PermissionHelper::check('/units/default/index')): ?>
                    <?= Html::a('<i class="fe fe-slack"></i><div class="home-menu-label">Danh mục đơn vị tính</div>', ['/units/default/index'], ['class' => 'home-menu-item home-menu-category']) ?>
                <?php endif; ?>               
            </div>
        </div>
    <?php endif; ?>

    <!-- PRODUCT -->
    <?php if (
        PermissionHelper::check('/products/default/index') ||
        PermissionHelper::check('/product_prices_unit/default/index') 
    ): ?>
        <div class="home-panel">
            <h5><i class="fe fe-box text-warning"></i> Sản phẩm</h5>
            <div class="home-menu-grid">
                <?php if(PermissionHelper::check('/products/default/index')): ?>
                    <?= Html::a('<i class="fe fe-box"></i><div class="home-menu-label">Sản phẩm</div>', ['/products/default/index'], ['class' => 'home-menu-item home-menu-product']) ?>
                <?php endif; ?>
                <?php if(PermissionHelper::check('/product_prices_unit/default/index')): ?>
                    <?= Html::a('<i class="fe fe-package"></i><div class="home-menu-label">Đóng gói mới</div>', ['/product_prices_unit/default/index'], ['class' => 'home-menu-item home-menu-product']) ?>
                 <?php endif; ?>    
            </div>
        </div>
    <?php endif; ?>

    <!-- INVOICE -->
    <?php if (
       PermissionHelper::check('/invoice/default') 
    ): ?>
        <div class="home-panel">
            <h5><i class="fe fe-file-plus text-warning"></i> Hóa đơn</h5>
            <div class="home-menu-grid">
                <?php if(PermissionHelper::check('/invoice/default')): ?>
                    <?= Html::a('<i class="fe fe-file-plus"></i><div class="home-menu-label">Hóa đơn</div>', ['/invoice/default'], ['class' => 'home-menu-item home-menu-invoice']) ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>   
    
    <!-- ALUMINUM -->
    <?php if (
        PermissionHelper::check('/alsystems/default') ||
        PermissionHelper::check('/alaluminummaterials/default') ||
        PermissionHelper::check('/alorders/default') || 
        PermissionHelper::check('/alcutgroups/default') || 
        PermissionHelper::check('/alscrapaluminum/default') || 
        PermissionHelper::check('/alreuselog/default')
    ): ?>
        <div class="home-panel">
            <h5><i class="fa-solid fa-industry"></i> Quản lý nhôm</h5>
            <div class="home-menu-grid">
                <?php if(PermissionHelper::check('/alsystems/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-boxes-packing"></i><div class="home-menu-label">Hệ thống nhôm</div>', ['/alsystems/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
                <?php if(PermissionHelper::check('/alaluminummaterials/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-cube"></i><div class="home-menu-label">Vật liệu nhôm đầu vào</div>', ['/alaluminummaterials/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
                 <?php if(PermissionHelper::check('/alorders/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-box-open"></i><div class="home-menu-label">Đơn hàng / công trình cần cắt</div>', ['/alorders/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
                <?php if(PermissionHelper::check('/alcutgroups/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-scissors"></i><div class="home-menu-label">Nhóm cắt của từng đơn hàng</div>', ['/alcutgroups/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
                <?php if(PermissionHelper::check('/alscrapaluminum/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-recycle"></i><div class="home-menu-label">Nhôm vụn còn dư sau khi cắt</div>', ['/alscrapaluminum/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
                <?php if(PermissionHelper::check('/alreuselog/default')): ?>
                    <?= Html::a('<i class="fa-solid fa-arrows-rotate"></i><div class="home-menu-label">Nhật ký tái sử dụng nhôm vụn</div>', ['/alreuselog/default'], ['class' => 'home-menu-item home-menu-aluminum']) ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>  

</div>

<?php
$this->title = 'Trợ lý AI - Quy chế đào tạo';
?>

<style>
/* Nút bật chat */
#chat-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #0d6efd;
  color: white;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  cursor: pointer;
  z-index: 9999;
  transition: all 0.3s ease;
}
#chat-toggle:hover { background-color: #0b5ed7; transform: scale(1.1); }

/* Hộp chat popup */
#chat-popup {
  position: fixed;
  bottom: 90px;
  right: 20px;
  width: 380px;
  max-height: 500px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 9998;
}

/* Header */
#chat-header {
  background: #0d6efd;
  color: white;
  padding: 10px;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
#chat-close {
  background: transparent;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
}

/* Nội dung chat */
#chat-box {
  padding: 10px;
  height: 340px;
  overflow-y: auto;
  background: #f8f9fa;
  font-size: 14px;
}
.user-msg { text-align: right; margin: 5px 0; color: #0d6efd; }
.ai-msg { text-align: left; margin: 5px 0; color: #198754; }

/* Input */
#chat-input-area {
  display: flex;
  border-top: 1px solid #ddd;
}
#user-input {
  flex: 1;
  border: none;
  padding: 10px;
  outline: none;
}
#send-btn {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0 15px;
  cursor: pointer;
}
#send-btn:hover { background: #0b5ed7; }
.text-danger { color: red; }
</style>

<!-- Nút bật chat -->
<div id="chat-toggle">
  <i class="fas fa-robot fa-lg"></i>
</div>

<!-- Popup chat -->
<div id="chat-popup">
  <div id="chat-header">
    <span><i class="fas fa-comments"></i> Trợ lý AI</span>
    <button id="chat-close">&times;</button>
  </div>
  <div id="chat-box"></div>
  <div id="chat-input-area">
    <input type="text" id="user-input" placeholder="Nhập câu hỏi về quy chế...">
    <button id="send-btn"><i class="fas fa-paper-plane"></i></button>
  </div>
</div>

<?php
$js = <<<JS
// Toggle chat popup
$('#chat-toggle').click(function() {
  $('#chat-popup').toggle();
});

$('#chat-close').click(function() {
  $('#chat-popup').hide();
});

// Gửi tin nhắn khi click hoặc nhấn Enter
$('#send-btn').click(sendMessage);
$('#user-input').keypress(function(e) {
  if(e.which == 13) sendMessage();
});

function sendMessage() {
  let question = $('#user-input').val().trim();
  if(!question) return;
  
  // Hiển thị câu hỏi
  $('#chat-box').append('<div class="user-msg"><b>Bạn:</b> ' + question + '</div>');
  $('#user-input').val('');
  $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);

  // Gọi API FastAPI
  $.ajax({
    url: 'http://127.0.0.1:8000/ask',
    method: 'GET',
    data: { question: question },
    success: function(res) {
      if(res.answer) {
        $('#chat-box').append('<div class="ai-msg"><b>AI:</b> ' + res.answer + '</div>');
      } else if(res.error) {
        $('#chat-box').append('<div class="ai-msg text-danger"><b>Lỗi:</b> ' + res.error + '</div>');
      }
      $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    },
    error: function(xhr, status, err) {
      $('#chat-box').append('<div class="ai-msg text-danger"><b>Lỗi:</b> Không kết nối được AI.</div>');
      $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }
  });
}
JS;
$this->registerJs($js);
?>
