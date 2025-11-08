<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Nhập liệu vật liệu nhôm & giá';
?>

<div class="aluminum-form p-3 border rounded bg-light">

<?php $form = ActiveForm::begin(); ?>

<h5 class="mb-3">Thông tin vật liệu nhôm</h5>
<div class="row">
    <div class="col-md-3"><?= $form->field($model, 'code')->textInput(['maxlength'=>true,'placeholder'=>'Mã vật liệu']) ?></div>
    <div class="col-md-3"><?= $form->field($model, 'name')->textInput(['maxlength'=>true,'placeholder'=>'Tên vật liệu']) ?></div>
    <div class="col-md-2"><?= $form->field($model, 'length')->input('number',['min'=>0,'placeholder'=>'Chiều dài (mm)']) ?></div>
    <div class="col-md-2"><?= $form->field($model, 'stock_quantity')->input('number',['min'=>0,'placeholder'=>'Số lượng tồn']) ?></div>
    <div class="col-md-2"><?= $form->field($model, 'stock_length')->input('number',['min'=>0,'placeholder'=>'Chiều dài tồn (mm)']) ?></div>
</div>

<div class="row">
    <div class="col-md-3"><?= $form->field($model, 'unit_price')->textInput(['placeholder'=>'Đơn giá','class'=>'form-control price-input text-end']) ?></div>
    <div class="col-md-9"><?= $form->field($model, 'note')->textarea(['rows'=>2,'placeholder'=>'Ghi chú']) ?></div>
</div>

<hr>
<h5 class="mb-2">Bảng giá vật liệu & nhân công</h5>
<button type="button" class="btn btn-sm btn-primary mb-2" id="add-price-row">➕ Thêm giá</button>

<div class="table-responsive">
    <table class="table table-bordered table-sm" id="pricing-table">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th>Mã vật tư</th>
                <th>Tên vật tư</th>
                <th>Đơn vị</th>
                <th>Giá cơ bản</th>
                <th>Nhân công</th>
                <th>% lợi nhuận</th>
                <th>Ghi chú</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dữ liệu cũ hoặc mới sẽ append bằng JS -->
        </tbody>
    </table>
</div>

<div class="mt-3">
    <?= Html::submitButton('Lưu', ['class'=>'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'
// format number VNĐ
function formatNumber(value) {
    value = value.replace(/\D/g,'');
    return value.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
}

// tạo 1 row HTML (index placeholder sẽ được renumber sau)
function createPriceRow(idx) {
    return `<tr>
        <td class="stt">${idx+1}</td>
        <td><input type="text" name="pricing[${idx}][item_code]" class="form-control"></td>
        <td><input type="text" name="pricing[${idx}][item_name]" class="form-control"></td>
        <td><input type="text" name="pricing[${idx}][unit]" class="form-control"></td>
        <td><input type="text" name="pricing[${idx}][base_price]" class="form-control price-input text-end"></td>
        <td><input type="text" name="pricing[${idx}][labor_cost]" class="form-control price-input text-end"></td>
        <td><input type="text" name="pricing[${idx}][profit_percent]" class="form-control text-end"></td>
        <td><input type="text" name="pricing[${idx}][note]" class="form-control"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm remove-price">🗑</button></td>
    </tr>`;
}

// renumber: cập nhật STT và sửa tất cả name theo index tuần tự
function renumberIndexes() {
    $('#pricing-table tbody tr').each(function(i){
        // cập nhật STT hiển thị
        $(this).find('td.stt').text(i+1);

        // cập nhật name cho tất cả input/select/textarea trong dòng
        $(this).find('input, select, textarea').each(function(){
            var name = $(this).attr('name');
            if (!name) return;
            // thay [số] đầu tiên bằng [i]
            var newName = name.replace(/\[\d+\]/, '[' + i + ']');
            $(this).attr('name', newName);
        });
    });
}

// khi click thêm dòng
$('#add-price-row').on('click', function(){
    // tạo row tạm với index = current length (sẽ được renumber ngay)
    let idx = $('#pricing-table tbody tr').length;
    $('#pricing-table tbody').append(createPriceRow(idx));
    renumberIndexes(); // chuẩn hóa tất cả name (quan trọng)
});

// xóa dòng
$(document).on('click', '.remove-price', function(){
    $(this).closest('tr').remove();
    renumberIndexes();
});

// định dạng VNĐ khi gõ
$(document).on('input', '.price-input', function(){
    let input = $(this);
    let cursorPos = input.prop('selectionStart');
    let beforeLen = input.val().length;
    let v = input.val();
    v = formatNumber(v);
    input.val(v);
    let afterLen = input.val().length;
    let newPos = cursorPos + (afterLen - beforeLen);
    input[0].setSelectionRange(newPos, newPos);
});

// nếu muốn: khi load dữ liệu server-side có sẵn (render rows), gọi renumberIndexes() để chuẩn hóa
$(function(){
    renumberIndexes();
});
JS
);
?>
