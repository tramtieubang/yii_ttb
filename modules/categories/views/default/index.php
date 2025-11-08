<?php
use yii\helpers\Url;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use kartik\grid\GridView;
use cangak\ajaxcrud\CrudAsset; 
use cangak\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\categories\models\CategoriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý danh sách';
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->params['showSearch'] = false;
Yii::$app->params['showView'] = false;
//CrudAsset::register($this);

?>
<?php if(Yii::$app->params['showSearch']):?><div class="card border-default" id="divFilterExtend">
	<div class="card-header rounded-bottom-0 card-header text-dark" id="simple">
		<h5 class="mt-2"><i class="fe fe-search"></i> Tìm kiếm</h5>
	</div>
	<div class="card-body">
		<div class="expanel expanel-default">
			<div class="expanel-body">
				<?php 
                    echo $this->render("_search", ["model" => $searchModel]);
                ?>			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php Pjax::begin([
    'id'=>'myGrid',
    'timeout' => 10000,
    'formSelector' => '.myFilterForm'
]); ?>

<div class="categories-form-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    '
                    <div class="dropdown">
						<button aria-expanded="false" aria-haspopup="true" class="btn dropdown-toggle" data-bs-toggle="dropdown" type="button"><i class="fa fa-navicon"></i></button>
						<div class="dropdown-menu tx-13" style="">
							<h6 class="dropdown-header tx-uppercase tx-11 tx-bold bg-info tx-spacing-1">
								Chọn chức năng</h6>'
                    .
                    Html::a('<i class="fas fa fa-plus" aria-hiddi="true"></i> Thêm mới', ['create'],
                        ['role'=>'modal-remote','title'=> 'Thêm mới','class'=>'dropdown-item'])
                    .
                    Html::a('<i class="fas fa fa-sync" aria-hidden="true"></i> Tải lại', [''],
                        ['data-pjax'=>1, 'class'=>'dropdown-item', 'title'=>'Tải lại'])
                    .
                    Html::a('<i class="fas fa fa-trash" aria-hidden="true"></i>&nbsp; Xóa danh sách',
                        ["bulkdelete"],
                        [
                            'class'=>'dropdown-item text-secondary',
                            'role'=>'modal-remote-bulk',
                            'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                            'data-request-method'=>'post',
                            'data-confirm-title'=>'Xác nhận xóa?',
                            'data-confirm-message'=>'Bạn có chắc muốn xóa?',
                            'data-modal-size' => 'large',
                        ])
                    .
                    Html::a('<i class="fa-solid fa-circle-plus me-2" aria-hiddi="true"></i> Thêm từ excel', ['/categories/import-excel/view'],
                        [
                            'role'=>'modal-remote',
                            'title'=> 'Import Excel',
                            'class'=>'dropdown-item',
                            'data-pjax' => 0,
                            'role' => 'modal-remote-2',      // để ajaxcrud nhận 

                        ])
                    .
                    '
						</div>
					</div>
                    '.
                    '{export}'
                ],
            ],             
            'striped' => false,
            'condensed' => true,
            'responsive' => false,
            'panelHeadingTemplate'=>'<div style="width:100%;"><div class="float-start mt-2 text-primary">{title}</div> <div class="float-end">{toolbar}</div></div>',
            'panelFooterTemplate'=>'<div style="width:100%;"><div class="float-start">{summary}</div><div class="float-end">{pager}</div></div>',
            'summary'=>'Tổng: {totalCount} dòng dữ liệu',
            'panel' => [
                'headingOptions'=>['class'=>'card-header rounded-bottom-0 card-header text-dark'],
                'heading' => '<i class="typcn typcn-folder-open"></i> XEM DANH SÁCH',
                'before'=>false,
            ],
            'export'=>[
                'options' => [
                    'class' => 'btn'
                ]
            ]          
        ])?>
    </div>
</div>

<?php Pjax::end(); ?>

<!--
<script>
$(document).on('hidden.bs.modal', '.modal', function() {
    if ($('.modal.show').length > 0) {
        $('body').addClass('modal-open');
    }
});
</script>
                -->
<?php
/*
$this->registerJs("
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    });
");
*/
?>

<?php
$this->registerJs("
    // Xếp chồng nhiều modal
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(() => {
            $('.modal-backdrop').not('.modal-stack')
                .css('z-index', zIndex - 1)
                .addClass('modal-stack');
        }, 0);
    });

    // Reset aria-hidden để tránh cảnh báo khi focus
    $(document).on('shown.bs.modal', '.modal', function () {
        $(this).attr('aria-hidden', 'false');
    });

    // Giữ lại scroll nếu còn modal khác đang mở
    $(document).on('hidden.bs.modal', '.modal', function () {
        $(this).attr('aria-hidden', 'true');
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    });
");
?>


<?php
// Modal 1
Modal::begin([
   'id' => 'ajaxCrudModal',
   'options' => [
        'tabindex' => false // important for Select2 to work properly
   ],
   'dialogOptions'=>['class'=>'modal-lg'],
   'closeButton'=>['label'=>'<span aria-hidden="true">×</span>'],
   'footer'=>'',// always need it for jquery plugin
]);
Modal::end();

// Modal 2
Modal::begin([
   'id' => 'ajaxCrudModal2',
   'options' => [
        'tabindex' => false
   ],
   'dialogOptions'=>['class'=>'modal-lg'],
   'closeButton'=>['label'=>'<span aria-hidden="true">×</span>'],
   'footer'=>'',
]);
Modal::end();

// Modal 3
Modal::begin([
   'id' => 'ajaxCrudModal3',
   'options' => [
        'tabindex' => false
   ],
   'dialogOptions'=>['class'=>'modal-lg'],
   'closeButton'=>['label'=>'<span aria-hidden="true">×</span>'],
   'footer'=>'',
]);
Modal::end();
