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
                                    value="<?= number_format($price->price, 0, ',', '.') ?>"
                                    class="form-control text-end price-input track-change"
                                    data-original="<?= number_format($price->price, 0, ',', '.') ?>"
                                    autocomplete="off"
                                    inputmode="numeric"
                                    placeholder="0">
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
    // Khi người dùng thay đổi giá tiền
    $(document).on('input', '.track-change', function() {
        const input = $(this);
        //let ip = input.val() + ' -- ' + input.data('original'); alert(ip);
        const original = (input.data('original') ?? '').toString().replace(/\D/g, '');
        const current = input.inputmask ? input.inputmask('unmaskedvalue') : (input.val() ?? '').replace(/\D/g, '');
 
        //let ip1 = input.val() + ' -- ' + input.data('original'); alert(ip1);

        if (current !== original) {
            input.css({'background-color': '#fff3cd', 'border-color': '#ffca2c'});
        } else {
            input.css({'background-color': '', 'border-color': ''});
        }
    });

    // Khi người dùng thay đổi datetime
    $(document).on('change input', '.datetime-input', function() {
        const input = $(this);
        const original = (input.data('original') ?? '').toString();
        const current = (input.val() ?? '').toString();

        if (current !== original) {
            input.css({'background-color': '#fff3cd', 'border-color': '#ffca2c'});
        } else {
            input.css({'background-color': '', 'border-color': ''});
        }
    });

    // Định dạng số có dấu chấm phân cách
    function formatNumber(value) {
        value = value.replace(/\D/g, '');
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Theo dõi thay đổi
    $(document).on('input', '.price-input', function(e) {
        const input = $(this);
        const cursorPos = input.prop('selectionStart'); // lưu vị trí con trỏ
        const beforeLength = input.val().length;

        // Lấy chuỗi gốc, định dạng lại
        let value = input.val();
        value = formatNumber(value);

        input.val(value);

        // Giữ nguyên vị trí con trỏ
        const afterLength = input.val().length;
        const newPos = cursorPos + (afterLength - beforeLength);
        input[0].setSelectionRange(newPos, newPos);

        // So sánh thay đổi
        const original = (input.data('original') ?? '').replace(/\D/g, '');
        const current = value.replace(/\D/g, '');

        if (current !== original) {
            input.css({'background-color': '#fff3cd', 'border-color': '#ffca2c'});
        } else {
            input.css({'background-color': '', 'border-color': ''});
        }
    });

    // Reinit khi mở modal AjaxCrud
    $(document).on('shown.bs.modal', '#ajaxCrudModal', function() {
        $('.price-input').trigger('input');
    });

    // --- AJAX lưu lại ---
    /* $(document).on('click', '#btn-save-price', function(e) {
        e.preventDefault();

        const form = $('#price-form');
        const btn = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                btn.prop('disabled', true).text('Đang lưu...!');
            },
            success: function(res) {
                //alert(res.success);
                if (res.success) {
                    toastr.success(res.message);

                    // 🔄 Reload lại gridview ajaxcrud
                    if ($.pjax) {
                        $.pjax.reload({container: '#crud-datatable-pjax'});
                    }

                    // ✅ Đóng modal
                    $('#ajaxCrudModal').modal('hide');
                } else {
                    toastr.error(res.message || 'Lưu không thành công.');
                }
            },
            error: function() {
                toastr.error('Không thể kết nối máy chủ.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Lưu lại');
            }
        });
    }); */


});

</script>
