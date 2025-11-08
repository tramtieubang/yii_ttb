<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap5\Modal;
use app\modules\alprofiles\models\AlProfilesSearch;

$this->title = 'Danh sách hệ nhôm';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.card-white {
    background-color: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

/* --- HEADER --- */
.card-header-custom {
    background-color: #ffffff; /* Màu trắng tinh */
    border-bottom: 1px solid #d1d5db;
    padding: 16px 22px;
    font-weight: 700;
    font-size: 1.05rem;
    color: #111827;
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Biểu tượng đầu dòng */
.card-header-custom i {
    color: #2563eb;
    margin-right: 6px;
}

/* --- BUTTONS --- */
.card-header-custom .btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.card-header-custom .btn-outline-primary:hover {
    background-color: #2563eb;
    color: #fff;
} *

.card-header-custom .btn-outline-secondary:hover {
    background-color: #6b7280;
    color: #fff;
}

/* --- FOOTER --- */
.card-footer-custom {
    background-color: #ffffff;
    border-top: 1px solid #e5e7eb;
    padding: 10px 16px;
    border-bottom-left-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
    font-size: 0.875rem;
    color: #6b7280;
    text-align: right;
}

</style>

<div class="al-systems-index">
   
    <?php Pjax::begin([
        'id' => 'crud-datatable-pjax',
        'timeout' => 10000,
        'formSelector' => '.myFilterForm'
    ]); ?>
    

    <div class="card-white">
       
        <!-- GridView -->
        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id' => 'al-systems-grid',
                'dataProvider' => $dataProvider,
                'pjax' => false,
                'panel' => false,
                'summary' => false,
                'responsive' => true,
                'striped' => false,
                'condensed' => true,
                'hover' => true,
                'columns' => require(__DIR__.'/_columns.php'),
                'toolbar'=> [
                    ['content'=>
                        Html::a('<i class="fas fa fa-plus" aria-hidden="true"></i> Thêm hệ nhôm', ['create'],
                        ['role'=>'modal-remote','title'=> 'Thêm mới Users','class'=>'btn btn-outline-primary']).
                        Html::a('<i class="fas fa fa-sync" aria-hidden="true"></i> Tải lại', [''],
                        ['data-pjax'=>1, 'class'=>'btn btn-outline-primary', 'title'=>'Tải lại']).
                      
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
                    //'heading' => '<i class="fas fa fa-list" aria-hidden="true"></i> Danh sách',
                    'heading' => $this->render('//layouts\menus/gridview_heading'),
                    'headingOptions' => ['class'=>'card-header'],
                    'before'=>'<em>* '.Html::encode($this->title).'</em>',
                    '<div class="clearfix"></div>',
                ],  

            ]); ?>
        </div>       

       
    </div>

    <?php Pjax::end(); ?>
</div>

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
    
    // ✅ Lắng nghe mọi AJAX phản hồi (khi thêm/sửa/xóa)
$(document).on('ajaxComplete', function (event, xhr) {
    let response;
    try {
        response = JSON.parse(xhr.responseText);
    } catch (e) {
        return; // Không phải JSON, bỏ qua
    }

    // ✅ Nếu có systemId → chỉ reload ExpandRow tương ứng
    if (response.system_id) {
        const $row = $('tr[data-key="' + response.system_id + '"]');

        // Nếu hàng đang expand thì reload nội dung con
        if ($row.hasClass('kv-state-expanded')) {
            const $expandCell = $row.next('.kv-expand-detail-row');
            const $expandContainer = $expandCell.find('.kv-detail-content');

            if ($expandContainer.length) {
                $.pjax.reload({
                    container: $expandContainer.find('.pjax-profiles-grid').attr('id'),
                    async: false
                });
            } else {
                // Nếu không có PJAX con, fallback: đóng mở lại
                const expandBtn = $row.find('.kv-expand-icon');
                expandBtn.click();
                setTimeout(() => expandBtn.click(), 600);
            }
        }
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
   'dialogOptions'=>['class'=>'modal-xl'],
   'closeButton'=>['label'=>'<span aria-hidden=\'true\'>×</span>'],
   'id'=>'ajaxCrudModal3',
    'footer'=>'',// always need it for jquery plugin
])?>

<?php Modal::end(); ?>
