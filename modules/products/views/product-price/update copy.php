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
$(document).ready(function() {

    // Format chuẩn VNĐ khi blur
    function formatCurrency(value) {
        if(!value) return '0,00';

        // Giữ lại số và dấu phẩy
        value = value.replace(/[^\d,]/g, '');

        // Chỉ lấy dấu phẩy đầu tiên
        const firstComma = value.indexOf(',');
        if(firstComma !== -1){
            let parts = [
                value.substring(0, firstComma),
                value.substring(firstComma + 1).replace(/,/g, '')
            ];
            value = parts[0] + ',' + parts[1];
        }

        let [integerPart, decimalPart] = value.split(',');
        integerPart = integerPart || '0';
        decimalPart = decimalPart || '';

        // Luôn 2 chữ số thập phân
        decimalPart = decimalPart.substring(0, 2);
        if(decimalPart.length === 0) decimalPart = '00';
        else if(decimalPart.length === 1) decimalPart += '0';

        // Thêm dấu chấm phân cách hàng nghìn
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return integerPart + ',' + decimalPart;
    }

    // Khi nhập: chỉ cho phép 0-9 và dấu phẩy đầu tiên, sau dấu phẩy max 2 số
    $(document).on('input', '.price-input', function() {
        const input = $(this);
        let value = input.val();

        // Giữ số và dấu phẩy đầu tiên
        value = value.replace(/[^0-9,]/g, '');
        const firstComma = value.indexOf(',');
        if(firstComma !== -1){
            let intPart = value.substring(0, firstComma);
            let decPart = value.substring(firstComma + 1).replace(/,/g, '');
            decPart = decPart.substring(0,2); // max 2 chữ số sau dấu phẩy
            value = intPart + (decPart ? ',' + decPart : ',');
        }

        input.val(value);

        // Highlight nếu khác giá gốc
        const original = (input.data('original') ?? '').replace(/\./g,'').replace(',', '.');
        const current = value.replace(/\./g,'').replace(',', '.');
        if(current !== original){
            input.css({'background-color': '#fff3cd', 'border-color': '#ffca2c'});
        } else {
            input.css({'background-color': '', 'border-color': ''});
        }
    });

    // Khi rời ô -> format chuẩn
    $(document).on('blur', '.price-input', function() {
        const input = $(this);
        input.val(formatCurrency(input.val()));

        const original = (input.data('original') ?? '').replace(/\./g,'').replace(',', '.');
        const current = input.val().replace(/\./g,'').replace(',', '.');
        if(current !== original){
            input.css({'background-color': '#fff3cd', 'border-color': '#ffca2c'});
        } else {
            input.css({'background-color': '', 'border-color': ''});
        }
    });

});




  
</script>
