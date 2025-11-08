<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header'=>'',
        'template' => '{view} {update} {delete}',
        'dropdown' => true,
        'dropdownOptions' => [
            'class' => 'float-right',
        ],
        'dropdownButton'=>[
            'label'=>'<i class="fe fe-settings floating"></i>',
            'class'=>'btn dropdown-toggle p-0'
        ],
        'vAlign'=>'middle',
        'width' => '20px',
        'urlCreator' => function($action, $model, $key, $index) {
        	return Url::to([$action,'id'=>$key]);
        },
        'visibleButtons' => [
            'view' => function ($model, $key, $index) {
                return Yii::$app->params['showView'];
            },
        ],
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','title'=>'Xem',
               'class'=>'btn ripple btn-primary btn-sm',
              'data-bs-placement'=>'top',
              'data-bs-toggle'=>'tooltip-primary'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Sửa', 
            'class'=>'btn ripple btn-info btn-sm',
            'data-bs-placement'=>'top',
            'data-bs-toggle'=>'tooltip-info'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Xóa', 
                      'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                      'data-request-method'=>'post',
                      'data-toggle'=>'tooltip',
                      'data-confirm-title'=>'Xác nhận xóa dữ liệu?',
                      'data-confirm-message'=>'Bạn có chắc chắn thực hiện hành động này?',
                       'class'=>'btn ripple btn-secondary btn-sm',
                       'data-bs-placement'=>'top',
                       'data-bs-toggle'=>'tooltip-secondary',
                       'data-modal-size' => 'large',
                    ], 

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
        'attribute'=>'invoice_number',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'customer_id',
        'value' => function ($model) {
            return $model->customer ? $model->customer->name : null;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'issue_date',
        'hAlign' => 'center',   
        'value' => function ($model) {
            $dt = new DateTime($model->issue_date, new \DateTimeZone('UTC')); 
            $dt->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
            return $dt->format('d/m/Y');
        },     
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'due_date',
        'hAlign' => 'center', 
        'value' => function ($model) {
            $dt = new DateTime($model->due_date, new \DateTimeZone('UTC')); 
            $dt->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
            return $dt->format('d/m/Y');
        },          
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'subtotal',
        'hAlign' => 'right',
        'value' => function ($model) {
            return number_format($model->subtotal, 2, ',', '.') . ' VNĐ';
        },
        'format' => 'raw',
        'contentOptions' => ['style' => 'text-align: right; white-space: nowrap;'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'format' => 'raw', // Cho phép HTML
        'label' => 'Xem',   // Tiêu đề cột
        'value' => function($model) {
            return \yii\helpers\Html::a(
                '<i class="fas fa-eye"></i>', // Icon
                ['default/view', 'id' => $model->id],
                [
                    'class' => 'btn btn-sm btn-outline-primary',
                    'title' => 'Xem hóa đơn',
                    'data-bs-toggle' => 'tooltip',
                    'role' => 'modal-remote' // nếu muốn mở modal ajax
                ]
            );
        },
        'hAlign' => 'center', // Căn giữa cột
        'vAlign' => 'middle',
        'width' => '80px',
    ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'discount_total',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'tax_total',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'total_amount',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'payment_method',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'notes',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
];   