<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="import-excel">
    <?php $form = ActiveForm::begin([
        'id' => 'form-import',
        'options' => ['enctype' => 'multipart/form-data'],
        'action' => Url::to(['import-excel/import']),
        'method' => 'post',
    ]); ?>

    <div class="mb-3">
        <?= Html::label('Chọn file Excel', 'excelFile', ['class' => 'form-label']) ?>
        <?= Html::fileInput('excelFile', null, ['class' => 'form-control', 'accept' => '.xlsx, .xls']) ?>
    </div>

    <div class="text-end">
        <?= Html::submitButton('Tải lên & Xem trước', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div id="preview-result" class="mt-4"></div>
</div>

<?php
$ajaxUrl = Url::to(['import-excel/import']);
$saveUrl = Url::to(['import-excel/save-import']);
$js = <<<JS
$('#form-import').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: '{$ajaxUrl}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#preview-result').html('<div class="text-muted">Đang đọc dữ liệu Excel...</div>');
        },
        success: function(res) {
            $('#preview-result').html(res.content);
        },
        error: function() {
            $('#preview-result').html('<div class="text-danger">Không thể đọc file Excel.</div>');
        }
    });
});


JS;
$this->registerJs($js);
?>
