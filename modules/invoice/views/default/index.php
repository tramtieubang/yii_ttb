<?php
use yii\helpers\Url;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use kartik\grid\GridView;
use cangak\ajaxcrud\CrudAsset; 
use cangak\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\invoice\models\InvoiceSearch */
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
                ?>			
            </div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php Pjax::begin([
    'id'=>'myGrid',
    'timeout' => 10000,
    'formSelector' => '.myFilterForm'
]); ?>

<div class="invoice-form-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="fas fa fa-plus" aria-hidden="true"></i> Thêm hóa đơn', ['create'],
                    ['role'=>'modal-remote','title'=> 'Thêm mới Users','class'=>'btn btn-outline-primary']).
                    Html::a('<i class="fas fa fa-sync" aria-hidden="true"></i> Tải lại', [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-outline-primary', 'title'=>'Tải lại']).
                    Html::a('<i class="fas fa fa-trash" aria-hidden="true"></i> Xóa đã chọn', ['bulkdelete'],
                    [
                        'class' => 'btn btn-outline-danger',
                        'role' => 'modal-remote-bulk',
                        'data-confirm' => false,
                        'data-method' => false,
                        'data-request-method' => 'post',
                        'data-confirm-title' => 'Xác nhận xóa?',
                        'data-confirm-message' => 'Bạn có chắc muốn xóa?',
                        'data-modal-size' => 'large',
                    ]).
                    //'{toggleData}'.
                    '{export}'
                ],
            ],          
            'striped' => false,
            'condensed' => true,
            'responsive' => true,   
            'panelHeadingTemplate'=>'{title}',
            'panelFooterTemplate'=>'{summary}',
            'summary'=>'Hiển thị dữ liệu {count}/{totalCount}, Trang {page}/{pageCount}',
            'panel' => [
                //'type' => 'primary', 
                'heading' => '<i class="fas fa fa-list" aria-hidden="true"></i> Danh sách',
                'headingOptions' => ['class'=>'card-header'],
                'before'=>'<em>* Danh sách hóa đơn</em>',
                '<div class="clearfix"></div>',
            ],      
        ])?>
    </div>
</div>

<?php Pjax::end(); ?>

<script>
/* $(document).on('hidden.bs.modal', '.modal', function() {
    if ($('.modal.show').length > 0) {
        $('body').addClass('modal-open');
    }
}); */
    document.addEventListener('hidden.bs.modal', function (event) {
        const modals = document.querySelectorAll('.modal.show');
        if (modals.length > 0) {
            document.body.classList.add('modal-open');
        }
    });                     
</script>

<?php Modal::begin([
   'options' => [
        'id'=>'ajaxCrudModal',
        'tabindex' => false // important for Select2 to work properly
   ],
   'dialogOptions'=>['class'=>'modal-xl'],
   'closeButton'=>['label'=>'<span aria-hidden=\'true\'>×</span>'],
   'id'=>'ajaxCrudModal',
    'footer'=>'',// always need it for jquery plugin
])?>

<?php Modal::end(); ?>

<?php Modal::begin([
   'options' => [
        'id'=>'ajaxCrudModal2',
        'tabindex' => false // important for Select2 to work properly
   ],
   'dialogOptions'=>['class'=>'modal-lg'],
   'closeButton'=>['label'=>'<span aria-hidden=\'true\'>×</span>'],
   'id'=>'ajaxCrudModal2',
    'footer'=>'',// always need it for jquery plugin
])?>

<?php Modal::end(); ?>

<?php Modal::begin([
   'options' => [
        'id'=>'ajaxCrudModal3',
        'tabindex' => false // important for Select2 to work properly
   ],
   'dialogOptions'=>['class'=>'modal-lg'],
   'closeButton'=>['label'=>'<span aria-hidden=\'true\'>×</span>'],
   'id'=>'ajaxCrudModal3',
    'footer'=>'',// always need it for jquery plugin
])?>

<?php Modal::end(); ?>
