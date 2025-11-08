<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;
?>

<style>
    /* Tiêu đề bảng màu xám nhạt, chữ đậm */
    #new-edit thead th {
        background-color: #f2f2f2; /* xám nhạt */
        color: #333;               /* chữ đen nhạt */
        text-align: center;        /* canh giữa */
        font-weight: 600;          /* chữ đậm vừa phải */
    }
</style>

<div class="update-product-price-form">
    <?php $form = ActiveForm::begin([
        'id' => 'update-product-price-form',
        'enableAjaxValidation' => false,
        'options' => ['data-pjax' => true],
        'action' => ['/products/product-price/update'], // route xử lý POST
    ]); ?>

    <div class="table-responsive">
        <table class="table table-bordered border text-nowrap mb-0" id="new-edit">
            <thead>
                <tr>
                    <th>SẢN PHẨM</th>
                    <th>ĐƠN VỊ</th>
                    <th>GIÁ</th>
                    <th>NGÀY CẬP NHẬT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestPrices as $price): ?>
                    <tr>
                        <td><?= Html::encode($model->name ?? '(chưa có)') ?></td>
                        <td><?= Html::encode($price->unit->name ?? '(chưa có)') ?></td>

                        <!-- Ô nhập giá -->
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text"
                                name="details[<?= $price->id ?>][price]"
                                value="<?= number_format($price->price, 2, ',', '.') ?>" 
                                class="form-control text-end price-input track-change"
                                data-original="<?= number_format($price->price, 2, ',', '.') ?>"
                                autocomplete="off"
                                placeholder="0,00">

                                <span class="input-group-text">VNĐ</span>

                        </div>
                    </td>
                        <!-- Ô nhập datetime -->
                        <td>
                            <input type="datetime-local"
                                name="datetime[<?= $price->id ?>][datetime]"
                                value="<?= date('Y-m-d\TH:i', strtotime($price->datetime)) ?>"
                                class="form-control form-control-sm track-change datetime-input"
                                data-original="<?= date('Y-m-d\TH:i', strtotime($price->datetime)) ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>


$(document).ready(function(){

    function formatWhileTyping(value) {
        if(!value) return '';

        let parts = value.split(',');
        let intPart = parts[0].replace(/\D/g,''); // chỉ giữ số nguyên
        let decPart = parts[1] ? parts[1].replace(/\D/g,'').substring(0,2) : '';

        // Thêm dấu chấm phân cách hàng nghìn cho phần nguyên
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return intPart + (decPart ? ',' + decPart : '');
    }

    function highlightIfChanged(input){
        const original = (input.data('original') ?? '').replace(/\./g,'').replace(',', '.');
        const current = input.val().replace(/\./g,'').replace(',', '.');
        if(current !== original){
            input.css({'background-color': '#fff3cd','border-color':'#ffca2c'});
        } else {
            input.css({'background-color':'','border-color':''});
        }
    }

    $(document).on('input', '.price-input', function(){
        const cursorPos = this.selectionStart;
        const beforeLength = $(this).val().length;

        // CHỈ format phần nguyên, không xóa dấu phẩy
        let val = $(this).val();
        let commaIndex = val.indexOf(',');
        let intPart = commaIndex >= 0 ? val.substring(0, commaIndex) : val;
        let decPart = commaIndex >= 0 ? val.substring(commaIndex + 1) : '';

        intPart = intPart.replace(/\D/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        $(this).val(intPart + (decPart !== '' ? ',' + decPart : ''));

        const afterLength = $(this).val().length;
        const newPos = cursorPos + (afterLength - beforeLength);
        this.setSelectionRange(newPos, newPos);

        highlightIfChanged($(this));
    });

    $(document).on('blur', '.price-input', function(){
        let val = $(this).val();
        let parts = val.split(',');
        if(parts.length === 1) val += ',00';
        else if(parts[1].length === 0) val += '00';
        else if(parts[1].length === 1) val += '0';

        $(this).val(formatWhileTyping(val));
        highlightIfChanged($(this));
    });

});





</script>
