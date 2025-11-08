<?php

use app\modules\user_management\user\models\UserForm;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

return [
    // Cột checkbox chọn nhiều hàng
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],

    // Cột hành động chính (xem, sửa, xóa)
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => '',
        'template' => '{view} {update} {delete}',
        'dropdown' => true,
        'dropdownOptions' => ['class' => 'float-right'],
        'dropdownButton' => [
            'label' => '<i class="fe fe-settings floating"></i>',
            'class' => 'btn dropdown-toggle p-0'
        ],
        'vAlign' => 'middle',
        'width' => '20px',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'visibleButtons' => [
            'view' => function ($model, $key, $index) {
                return Yii::$app->params['showView'];
            },
        ],
        'viewOptions' => [
            'role' => 'modal-remote',
            'title' => 'Xem',
            'class' => 'btn ripple btn-primary btn-sm',
            'data-bs-toggle' => 'tooltip',
            'data-bs-placement' => 'top',
        ],
        'updateOptions' => [
            'role' => 'modal-remote',
            'title' => 'Sửa',
            'class' => 'btn ripple btn-info btn-sm',
            'data-bs-toggle' => 'tooltip',
            'data-bs-placement' => 'top',
        ],
        'deleteOptions' => [
            'role' => 'modal-remote',
            'title' => 'Xóa',
            'data-confirm' => false,
            'data-method' => false,
            'data-request-method' => 'post',
            'data-confirm-title' => 'Xác nhận xóa dữ liệu?',
            'data-confirm-message' => 'Bạn có chắc chắn thực hiện hành động này?',
            'class' => 'btn ripple btn-secondary btn-sm',
            'data-bs-toggle' => 'tooltip',
            'data-bs-placement' => 'top',
            'data-modal-size' => 'large',
        ],
    ],

    // Cột số thứ tự
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

    // Cột superadmin (ẩn với user thường)
    [
        'class' => 'webvimark\components\StatusColumn',
        'attribute' => 'superadmin',
        'visible' => Yii::$app->user->isSuperadmin,
    ],

    // Tên đăng nhập
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'username',
        'header' => 'Tên đăng nhập',
        'contentOptions' => ['style' => 'min-width:120px; white-space:nowrap;'],
    ],

    // Email
    [
        'attribute' => 'email',
        'format' => 'raw',
        'visible' => UserForm::hasPermission('viewUserEmail'),
        'header' => 'Email',
        'contentOptions' => ['style' => 'min-width:160px; white-space:nowrap;'],
    ],

    // Email đã xác nhận hay chưa
    [
        'class' => 'webvimark\components\StatusColumn',
        'attribute' => 'email_confirmed',
        'visible' => UserForm::hasPermission('viewUserEmail'),
        'header' => 'Email xác nhận',
    ],

    // Vai trò (Roles)
    [
        'attribute' => 'gridRoleSearch',
        'header' => 'Vai trò',
        'filter' => ArrayHelper::map(
            Role::getAvailableRoles(Yii::$app->user->isSuperAdmin),
            'name',
            'description'
        ),
        'value' => function (UserForm $model) {
            return implode(', ', ArrayHelper::map($model->roles, 'name', 'description'));
        },
        'format' => 'raw',
        'visible' => UserForm::hasPermission('viewUserRoles'),
        'contentOptions' => ['style' => 'min-width:150px; white-space:nowrap;'],
    ],

    // Cột hành động tùy chọn: Phân quyền + Đổi mật khẩu
    [
        'header' => 'Tùy chọn',
        'format' => 'raw',
        'value' => function (UserForm $model) {
            $html = '
            <div class="dropdown text-center">
                <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fe fe-settings"></i> Tùy chọn
                </button>
                <ul class="dropdown-menu text-start">';

            if (UserForm::canRoute('/user_management/user/user-permission/')) {
                $html .= '<li>' . GhostHtml::a(
                    '<i class="fa fa-user-shield me-1"></i> Phân quyền',
                    ['/user_management/user/user-permission/', 'id' => $model->id, 'user' => $model->username],
                    [
                        'class' => 'dropdown-item',
                        'data-pjax' => 0,
                        'role' => 'modal-remote-2',      // để ajaxcrud nhận dạng  👈 DÙNG role riêng để phân biệt modal 2
                        'title' => 'Vai trò và quyền hạn', // tooltip
                        'data-bs-toggle' => 'tooltip',
                        'data-modal-size' => 'modal-xl', //  modal cực lớn
                    ]
                ) . '</li>';
            }

            $html .= '<li>' . GhostHtml::a('<i class="fa fa-key me-1"></i> Đổi mật khẩu',
                ['/user_management/user/default/change-password', 'id' => $model->id],
                [
                    'class' => 'dropdown-item', 
                    'data-pjax' => 1,
                    'role'=>'modal-remote', // để ajaxcrud/modal-remote bắt sự kiện                  
                    'data-bs-toggle'=>'tooltip', // nếu Bootstrap 5
                    'title' => 'Đổi mật khẩu tài khoản', // thêm title
                ]
            ) . '</li>';

            $html .= '</ul></div>';
            return $html;
        },
        'contentOptions' => ['class' => 'text-center', 'style' => 'min-width:150px;'],
    ],

    // Trạng thái hoạt động
    [
        'class' => 'webvimark\components\StatusColumn',
        'attribute' => 'status',
        'header' => 'Trạng thái',
        'optionsArray' => [
            [UserForm::STATUS_ACTIVE, UserManagementModule::t('back', 'Hoạt động'), 'success'],
            [UserForm::STATUS_INACTIVE, UserManagementModule::t('back', 'Ngưng hoạt động'), 'warning'],
            [UserForm::STATUS_BANNED, UserManagementModule::t('back', 'Bị khóa'), 'danger'],
        ],
        'contentOptions' => ['class' => 'text-center'],
    ],
];
