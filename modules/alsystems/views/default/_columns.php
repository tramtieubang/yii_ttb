<?php

use app\modules\alprofiles\models\AlProfilesSearch;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

return [
     [
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'value' => fn() => GridView::ROW_COLLAPSED,
        'detail' => function($model) {
            $searchModel = new AlProfilesSearch();
            $searchModel->system_id = $model->id;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return Yii::$app->controller->renderPartial('_profiles_grid', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'system' => $model,
            ]);
        },
        'expandOneOnly' => true,
    ],
    ['class' => 'kartik\grid\SerialColumn'],
    'code',
    'name',
    'brand',
    'origin',
    'thickness',
    [
        'attribute' => 'status',
        'format' => 'html',
        'value' => function ($m) {
            $status = strtolower(trim((string)$m->status)); // chuẩn hóa chữ thường + bỏ khoảng trắng

            if (in_array($status, ['1', 'active'], true)) {
                return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Đang sử dụng</span>';
            }

            return '<span class="badge bg-secondary"><i class="fas fa-ban"></i> Không sử dụng</span>';
        },
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{view} {update} {delete}',
        'width' => '110px',
        'buttons' => [
            'view' => fn($url, $model) => Html::a('<i class="fas fa-eye"></i>', $url, [
                'class' => 'btn btn-sm btn-outline-info',
                'role' => 'modal-remote',
                'title' => 'Xem chi tiết',
            ]),
            'update' => fn($url, $model) => Html::a('<i class="fas fa-edit"></i>', $url, [
                'class' => 'btn btn-sm btn-outline-primary',
                'role' => 'modal-remote',
                'title' => 'Cập nhật',
            ]),
            'delete' => fn($url, $model) => Html::a('<i class="fas fa-trash"></i>', $url, [
                'class' => 'btn btn-sm btn-outline-danger',
                'role' => 'modal-remote-2',
                'data-request-method' => 'post',
                'data-confirm-title' => 'Xác nhận xóa?',
                'data-confirm-message' => 'Bạn có chắc muốn xóa hệ nhôm '.$model->name.' này?',
                'title' => 'Xóa',
            ]),
        ],
    ],
];   