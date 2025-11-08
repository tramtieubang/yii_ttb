<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/** @var $system app\models\AlSystems */

Pjax::begin([
    'id' => 'pjax-profiles-grid-' . $system->id,
    'options' => ['class' => 'pjax-profiles-grid'],
    'timeout' => 5000,
]);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="text-primary mb-0">
        <i class="fas fa-cube"></i> Thanh nhôm thuộc hệ: <?= Html::encode($system->name) ?>
    </h6>
    <?= Html::a('<i class="fas fa-plus"></i> Thêm mới', ['/alprofiles/default/create', 'system_id' => $system->id], [
        'class' => 'btn btn-sm btn-outline-primary',
        'role' => 'modal-remote-3',
        'title' => 'Thêm mới profile',
    ]) ?>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'attribute' => 'code',
            'label' => 'Mã thanh',
        ],
        [
            'attribute' => 'name',
            'label' => 'Tên thanh nhôm',
        ],
        [
            'attribute' => 'door_types',
            'label' => 'Loại cửa áp dụng',
        ],
        [
            'attribute' => 'unit_price',
            'format' => ['decimal', 0],
            'label' => 'Đơn giá (VNĐ/m)',
            'contentOptions' => ['class' => 'text-end'],
        ],
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => fn($m) => in_array(strtolower(trim((string)$m->status)), ['1', 'Active','active'], true)
                ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Đang sử dụng</span>'
                : '<span class="badge bg-secondary"><i class="fas fa-ban"></i> Không sử dụng</span>',
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view} {update} {delete}',
            'width' => '110px', // tăng chiều rộng đủ cho 3 nút
            'buttons' => [                
                'view' => function($url, $model, $key) {
                    return Html::a('<i class="fas fa-eye"></i>', ['/alprofiles/default/view', 'id' => $model->id], [
                        'class' => 'btn btn-primary btn-sm',
                        'role' => 'modal-remote',
                        'title' => 'Xem chi tiết',
                        'data-bs-toggle' => 'tooltip',
                        'data-bs-placement' => 'top',
                    ]);
                },
                'update' => fn($url, $model) => Html::a('<i class="fas fa-edit"></i>', ['/alprofiles/default/update', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-outline-primary',
                    'role' => 'modal-remote-3',
                    'title' => 'Cập nhật profile',
                ]),
                'delete' => fn($url, $model) => Html::a('<i class="fas fa-trash"></i>', ['/alprofiles/default/delete', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'role' => 'modal-remote-2',
                    'data-request-method' => 'post',
                    'data-confirm-title' => 'Xác nhận xóa?',
                    'data-confirm-message' => 'Bạn có chắc muốn xóa thanh nhôm này?',
                    'title' => 'Xóa',
                ]),
            ],
            'contentOptions' => ['class' => 'text-center'],
        ],
    ],

    /** ✅ Layout giữ phân trang & summary ở cuối **/
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center grid-footer-bar mt-2 pt-2 border-top'>
        <div>{pager}</div>
        <div class='small text-muted'>{summary}</div>
    </div>",

    'summary' => 'Hiển thị {count}/{totalCount} bản ghi',
    'responsive' => true,
    'condensed' => true,
    'striped' => false,
    'hover' => true,
]) ?>

<?php Pjax::end(); ?>

<?php
$this->registerCss("
    /* Footer */
    .grid-footer-bar {
        border-color: #e5e7eb !important;
        font-size: 13px;
    }
    .grid-footer-bar .pagination {
        margin: 0;
    }

    /* Hover hiệu ứng */
    table.kv-grid-table tbody tr:hover {
        background-color: #f1f7ff !important;
        transition: background-color 0.2s ease;
    }

    /* Nền trắng cho bảng */
    .kv-grid-table {
        background-color: #ffffff !important;
    }

    /* Viền nhẹ để rõ ràng */
    .kv-grid-table td, .kv-grid-table th {
        border-color: #e5e7eb !important;
    }
");
?>
