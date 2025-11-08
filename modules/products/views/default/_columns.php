<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'vAlign'=>'top',
        'width' => '20px',
    ],    
    [
        'class' => 'kartik\grid\SerialColumn',
        'vAlign'=>'top',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'category_id',
        'vAlign'=>'top',
        'value' => function ($model) {
            return $model->category ? $model->category->name : null;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
        'vAlign'=>'top',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute' => 'Đơn vị tính _ Giá _ Ngày',
        'header' => 'Đơn vị tính - Giá - Ngày', // tiêu đề hiển thị
        'hAlign'=>'top',
        'vAlign'=>'top',
        'format' => 'raw', // Cho phép xuống dòng
       
       'value' => function($model) {
            if (empty($model->latestProductPricesUnit)) {
                // Thong bao khong phai button
               /*  return '<div style="width:100%; text-align:center;"> 
                        <div style="
                            background: linear-gradient(135deg, #ff7b7b, #ff4c4c); // gradient đỏ đẹp 
                            color: #fff; // chữ trắng nổi bật 
                            font-weight:600; // chữ đậm 
                            padding:5px 12px; 
                            font-size:13px; 
                            text-align:center; 
                            border-radius:6px; // bo tròn chuyên nghiệp 
                            display:inline-block;
                            margin:0 auto;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2); // bóng nhẹ 
                            min-width:120px;">
                            Chưa cập nhật giá
                        </div>
                    </div>'; 
                */
                // Thong bao button
                return Html::a(
                    '<div style="
                        background: linear-gradient(135deg, #ff7b7b, #ff4c4c);
                        color: #fff;
                        font-weight:600;
                        padding:5px 12px;
                        font-size:13px;
                        text-align:center;
                        border-radius:6px;
                        display:inline-block;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        min-width:120px;">
                        Chưa cập nhật giá
                    </div>',
                    //['products/product-price/create', 'id' => $model->id], // route mở modal AjaxCrud
                    ['/products/product-price/create', 'productid' => $model->id], // route mở modal AjaxCrud
                    [
                        'role' => 'modal-remote', // ⚡ AjaxCrud sẽ tự bắt sự kiện
                        'title' => 'Cập nhật giá sản phẩm',
                        'data-bs-toggle' => 'tooltip',
                        'data-pjax' => 0,
                        'style' => 'text-decoration:none;', // bỏ gạch chân
                    ]
                );
            }

            $list = [];
            foreach ($model->latestProductPricesUnit as $pu) {
                $list[] = sprintf(
                    '<div style="margin-bottom:6px;">
                        <div style="display:flex;">
                            <div style="width:60px; text-align:left;">ĐVT:</div>
                            <div style="flex:1; text-align:right;">%s</div>
                        </div>
                        <div style="display:flex;">
                            <div style="width:60px; text-align:left;">Giá:</div>
                            <div style="flex:1; text-align:right;">%s</div>
                        </div>
                        <div style="display:flex;">
                            <div style="width:60px; text-align:left;">Ngày:</div>
                            <div style="flex:1; text-align:right;">%s</div>
                        </div>
                    </div>',
                    $pu->unit->name,
                    //Yii::$app->formatter->asCurrency($pu->price,'VNĐ'),
                   number_format($pu->price, 2, ',', '.') . ' VNĐ',
                    Yii::$app->formatter->asDate($pu->datetime, 'php:d/m/Y')
                );
            }

            return implode('<hr style="margin:2px 0;">', $list);
        },
        'format' => 'raw',

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'header'=>'Giá hiện tại',
        'format'=>'raw',
        'hAlign'=>'center',   // căn giữa ngang
        'vAlign'=>'top',
        'options' => [
            'width' => '55px',
        ],
        'value' => function($model) {
            return 
                GhostHtml::a('<i class="fe fe-settings floating"></i>', 
                    ['/products/product-price/edit', 'id' => $model->id], 
                    [
                        'role'=>'modal-remote', // để ajaxcrud/modal-remote bắt sự kiện
                        'title'=>'Cập nhật giá',                      
                        'data-bs-toggle'=>'tooltip', // nếu Bootstrap 5
                        'style' => '
                            display: inline-block;
                            border: 1px solid transparent;
                            border-radius: 6px;
                            padding: 4px 6px;
                            transition: all 0.2s;
                        ',
                        'onmouseover' => "this.style.borderColor='#717476ff'; this.style.backgroundColor='rgba(0,123,255,0.05)'",
                        'onmouseout' => "this.style.borderColor='transparent'; this.style.backgroundColor='transparent'",
                    ]
                );
        }
        /*
        [
        'value' => function (User $model) {
            return GhostHtml::a(
                UserModule::t('back', '<i class="pe-7s-config"></i>'),
                ['/user/user-permission/set', 'id' => $model->id],
                ['class' => 'btn btn-sm btn-primary', 'data-pjax' => 1, 'role' => 'modal-remote', 'title' => 'Phân quyền tài khoản']
            );
        },
        'visible' => User::canRoute('/user/user-permission/set'),
        'options' => [
            'width' => '10px',
        ],
    ],
        */
    ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'top',
        'width' => '200px',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => [
            'role' => 'modal-remote',
            'title' => 'View',
            'title' => 'Xem thông tin',
            'class' => 'btn ripple btn-primary btn-sm',
            'data-bs-placement' => 'top',
            'data-bs-toggle' => 'tooltip-primary'
        ],
        'updateOptions' => [
            'role' => 'modal-remote',
            'title' => 'Cập nhật dữ liệu',
            'class' => 'btn ripple btn-info btn-sm',
            'data-bs-placement' => 'top',
            'data-bs-toggle' => 'tooltip-info'
        ],
        'deleteOptions' => [
            'role' => 'modal-remote',
            'title' => 'Xóa dữ liệu này',
            'data-confirm' => false,
            'data-method' => false, // for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Xác nhận xóa dữ liệu?',
            'data-confirm-message' => 'Bạn có chắc chắn thực hiện hành động này?',
            'class' => 'btn ripple btn-secondary btn-sm',
            'data-bs-placement' => 'top',
            'data-bs-toggle' => 'tooltip-secondary'
        ],
    ],
];   