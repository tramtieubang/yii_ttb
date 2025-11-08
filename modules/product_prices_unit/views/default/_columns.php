<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],   
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product_id',
        'value' => function ($model) {
            return $model->product ? $model->product->name : null;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'unit_id',
        'value' => function ($model) {
            return $model->unit ? $model->unit->name : null;
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'price',        
        'value' => function ($model) {
            return number_format($model->price, 0, ',', '.') . ' VNĐ';
        },
        'hAlign' => 'right', // canh phải đẹp hơn cho cột tiền        
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'datetime',
        'value' => function ($model) {
            if (!$model->datetime) {
                return null;
            }
            $dt = new \DateTime($model->datetime, new \DateTimeZone('Asia/Ho_Chi_Minh'));
            return $dt->format('d/m/Y H:i:s');
        },
        'hAlign' => 'centrer',    
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
        'vAlign' => 'middle',
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