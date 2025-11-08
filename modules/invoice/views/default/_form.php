<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\modules\customers\models\CustomersForm;
use app\modules\products\models\ProductsForm;
use codenixsv\flatpickr\Flatpickr;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UrlRule;

/* @var $model app\modules\invoice\models\InvoiceForm */
/* @var $latestPrices ProductsForm[] */
?>

<style>
/* --- Các style giống trước, chỉ thêm z-index cho select2 --- */
.select2-container { z-index: 99999 !important; }

.invoice-form {
    background: #f7f9fb;
    padding: 5px 20px 20px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.invoice-section {
    background: #fff;
    border: 1px solid #e4e7eb;
    border-radius: 8px;
    margin-bottom: 14px;
    padding: 18px 20px;
    transition: box-shadow .2s;
}
.invoice-section:hover {
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.invoice-section h5 {
    font-size: 15px;
    font-weight: 600;
    color: #444;
    margin-bottom: 14px;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}

/* Giảm khoảng cách các dòng trong form */
.form-group {
    margin-bottom: 10px !important;
}

/* Giảm chiều cao input */
.form-control, .select2-selection--single {
    height: 34px !important;
    padding: 4px 8px !important;
    font-size: 13px;
}

/* Select2 nhỏ lại */
.select2-container--krajee .select2-selection {
    min-height: 34px !important;
}

/* Label nhỏ gọn */
.control-label {
    font-size: 13px;
    font-weight: 500;
    color: #555;
}

/* Bảng chi tiết hóa đơn */
.table thead th {
    background-color: #f8fafc;
    color: #333;
    font-weight: 600;
    font-size: 13px;
    padding: 8px;
}
.table td {
    padding: 6px;
    font-size: 13px;
}
.form-control-sm {
    padding: 4px 6px !important;
    height: 30px !important;
    font-size: 13px;
}

/* Tổng tiền */
.total-box {
    background: #f9fafb;
    border-radius: 8px;
    padding: 10px 16px;
    border: 1px solid #e6e8eb;
}
.total-box strong, .total-box h5 {
    font-weight: 600;
    font-size: 14px;
}
.text-primary {
    color: #007bff !important;
}

/* Nút hành động */
.btn-save {
    background: #28a745;
    border: none;
    color: white;
    font-weight: 500;
    padding: 8px 22px;
    border-radius: 6px;
    transition: 0.2s;
}
.btn-save:hover {
    background: #218838;
}
.btn-cancel {
    border: 1px solid #ccc;
    color: #555;
    background: #f8f9fa;
    font-weight: 500;
    padding: 8px 20px;
    border-radius: 6px;
    margin-left: 6px;
    transition: 0.2s;
}
.btn-cancel:hover {
    background: #e9ecef;
}
</style>

<div class="invoice-form">
    <?php $form = ActiveForm::begin([
        'id' => 'invoice-form',
        'method' => 'post',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,

    ]); ?>

    <!-- 🧾 Thông tin hóa đơn -->
    <div class="invoice-section">
        <h5><i class="fas fa-file-invoice text-success me-2"></i>Thông tin hóa đơn</h5>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'invoice_number')->textInput(['maxlength' => true, 'placeholder' => 'VD: HD-2025-001']) ?>
            </div>
            <div class="col-md-9">
                <?php
                    echo $form->field($model, 'customer_id', [
                        'template' => '{label} ' . Html::a(
                                                '<i class="fa fa-plus"></i>',
                                                ['/customers/default/create'],
                                                //['/invoice/default/customer'],
                                                [
                                                    'title' => 'Thêm khách hàng mới',
                                                    'class' => 'btn btn-outline-primary btn-sm rounded-circle',
                                                    'id' => 'btn-add-customer',
                                                    'style' => 'margin-left: 5px; padding: 0.25rem 0.35rem; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;',
                                                    'role' => 'modal-remote-2',      // để ajaxcrud nhận dạng  👈 DÙNG role riêng để phân biệt modal 2
                                                    'data-pjax' => 0,
                                                    'data-target' => '#ajaxCrudModal2', // 🔹 chỉ định modal thứ 2
                                                    'data-bs-toggle' => 'tooltip',
                                                    'data-bs-placement' => 'top',
                                                ]
                                            )  . "{input}{hint}{error}",
                    ])->widget(Select2::classname(), [
                        'data' => \yii\helpers\ArrayHelper::map(
                            CustomersForm::find()->all(), 
                            'id', 
                            function($model) {
                                return '<div style="display: flex;">
                                            <div style="width: 30px;">'.$model->id.'</div> 
                                            <div style="width: 150px;">'.$model->name.'</div>
                                            <div style="width: 100px;">'.$model->phone.'</div>
                                            <div>'.$model->address.'</div>
                                        </div>';
                            }
                        ),
                        'options' => [
                            'placeholder' => 'Chọn khách hàng...',
                        ],
                        'pluginOptions' => [
                            'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
                            'dropdownParent' => new JsExpression("$('#ajaxCrudModal .modal-body')"),
                            'allowClear' => true,
                        ],
                    ]);  
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList([
                    'Nháp' => '📝 Nháp',
                    'Chưa thanh toán' => '💰 Chưa thanh toán',
                    'Đã thanh toán' => '✅ Đã thanh toán',
                    'Đã hủy' => '❌ Đã hủy',
                ]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'payment_method')->dropDownList([
                        'Tiền mặt' => '💵 Tiền mặt',
                        'Chuyển khoản' => '🏦 Chuyển khoản',
                        'Thẻ' => '💳 Thẻ',
                        'Khác' => '❓ Khác',
                    ], ['prompt' => 'Chọn phương thức...']) 
                ?>
            </div>
            <div class="col-md-3">                
                <?php
                    $model->issue_date = $model->issue_date ?? date('Y-m-d');
                   echo $form->field($model, 'issue_date')->input('date') 
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    $model->due_date = $model->due_date ?? date('Y-m-d');
                   echo $form->field($model, 'due_date')->input('date') 
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'notes')->textarea(['rows' => 2, 'placeholder' => 'Ghi chú thêm (nếu có)...']) ?>
            </div>
        </div>
    </div>

    <!-- 📦 Chi tiết hóa đơn -->
    <div class="invoice-section">
        <div class="d-flex justify-content-between align-items-center">
            <h5>📦 Chi tiết hóa đơn</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn">
                ➕ Thêm dòng
            </button>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm" id="invoice-details-table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th>Tên sản phẩm</th>
                        <th style="width: 10%">SL</th>
                        <th style="width: 15%">Đơn giá</th>
                        <th style="width: 15%">Thành tiền</th>
                        <th style="width: 15%">Ghi chú</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- 💵 Tổng tiền -->
    <div class="invoice-section total-box text-end">
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-1">
                    <span>Tổng trước thuế:</span>
                    <strong id="subtotal-display">0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Giảm giá:</span>
                    <strong id="discount-display">0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Thuế:</span>
                    <strong id="tax-display">0.00</strong>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span><strong>Tổng cộng:</strong></span>
                    <h5 class="text-primary mb-0"><strong id="total-display">0.00</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden inputs để gửi dữ liệu lên server -->
    <?= $form->field($model, 'subtotal')->hiddenInput(['id'=>'subtotal'])->label(false) ?>
    <?= $form->field($model, 'discount_total')->hiddenInput(['id'=>'discount_total'])->label(false) ?>
    <?= $form->field($model, 'tax_total')->hiddenInput(['id'=>'tax_total'])->label(false) ?>
    <?= $form->field($model, 'total_amount')->hiddenInput(['id'=>'total_amount'])->label(false) ?>

    <!-- 🔘 Nút hành động -->
	<!-- <div class="form-group text-end mt-3">
		<?= Html::a('<i class="fas fa-arrow-left"></i> Quay lại', ['index'], [
			'class' => 'btn btn-outline-secondary action-btn',
		]) ?>
		<?= Html::submitButton('<i class="fas fa-save"></i> Lưu hóa đơn', [
			'class' => 'btn btn-primary action-btn',
		]) ?>
	</div> -->

    <?php ActiveForm::end(); ?>
</div>

<?php
// --- Chuẩn bị dữ liệu sản phẩm ---
$productOptions = [];
$latestPrices = ProductsForm::find()->with(['latestProductPricesUnit.unit'])->all();

foreach($latestPrices as $p){
    if(!empty($p->latestProductPricesUnit)){
        foreach($p->latestProductPricesUnit as $price){
            $productOptions[] = [
                'id' => $price->id,
                'text' => "{$p->name} - {$price->unit->name} - {$price->price}",
                'price' => $price->price
            ];
        }
    }
}

$productOptionsJson = json_encode($productOptions);
?>
<script>
$(document).ready(function(){

    const productOptions = <?= $productOptionsJson ?>;

    // --- Hàm format tiền kiểu Việt Nam ---
    function formatCurrency(number){
        if(!number) number = 0;
        return number.toLocaleString('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // --- Chuyển chuỗi "1.234,56" sang float ---
    function parseCurrency(str){
        if(!str) return 0;
        return parseFloat(str.replace(/\./g,'').replace(',','.')) || 0;
    }

    // --- Khởi tạo Select2 ---
    function initSelect2(el){
        el.select2({
            width:'100%',
            data: productOptions,
            placeholder:'Chọn sản phẩm...',
            allowClear:true,
            dropdownParent: $('#ajaxCrudModal .modal-body')
        });
    }

    // --- Thêm dòng mới ---
    $('#add-item-btn').on('click', function(){
        let rowCount = $('#invoice-details-table tbody tr').length + 1;
        let newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>
                    <select name="InvoiceDetail[${rowCount}][product_price_unit_id]" class="form-control form-control-sm select2-product">
                        <option></option>
                    </select>
                </td>
                <td>
                    <input type="number" name="InvoiceDetail[${rowCount}][quantity]" 
                           class="form-control form-control-sm text-center qty" value="1" min="1">
                </td>
                <td><input type="text" name="InvoiceDetail[${rowCount}][unit_price]" class="form-control form-control-sm text-end price" readonly></td>
                <td><input type="text" name="InvoiceDetail[${rowCount}][total]" class="form-control form-control-sm text-end total" readonly></td>
                <td><input type="text" name="InvoiceDetail[${rowCount}][note]" class="form-control form-control-sm"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        $('#invoice-details-table tbody').append(newRow);
        initSelect2($('#invoice-details-table tbody tr:last .select2-product'));
    });

    // --- Xóa dòng ---
    $(document).on('click', '.remove-item', function(){
        $(this).closest('tr').remove();
        renumberRows();
        updateTotals();
    });

    // --- Đánh lại STT ---
    function renumberRows(){
        $('#invoice-details-table tbody tr').each(function(index){
            $(this).find('td:first').text(index + 1);
        });
    }

    // --- Khi chọn sản phẩm ---
    $(document).on('select2:select', '.select2-product', function(){
        let row = $(this).closest('tr');
        let selectedId = $(this).val();
        let product = productOptions.find(p => p.id == selectedId);
        if(product){
            let price = parseFloat(product.price);
            row.find('.price').val(formatCurrency(price));
            let qty = parseFloat(row.find('.qty').val()) || 0;
            row.find('.total').val(formatCurrency(qty * price));
        }
        updateTotals();
    });

    // --- Khi thay đổi số lượng ---
    $(document).on('input', '.qty', function(){
        let row = $(this).closest('tr');
        let qty = parseFloat($(this).val()) || 0;
        let price = parseCurrency(row.find('.price').val());
        row.find('.total').val(formatCurrency(qty * price));
        updateTotals();
    });

    // --- Hàm cập nhật tổng ---
    function updateTotals(){
        let subtotal = 0;
        $('.total').each(function(){
            subtotal += parseCurrency($(this).val());
        });

        let discount = 0; // hoặc có thể lấy từ input khác nếu có
        let tax = 0;
        let total = subtotal - discount + tax;

        // --- Hiển thị ---
        $('#subtotal-display').text(formatCurrency(subtotal));
        $('#discount-display').text(formatCurrency(discount));
        $('#tax-display').text(formatCurrency(tax));
        $('#total-display').text(formatCurrency(total));

        // --- Hidden input ---
        $('#subtotal').val(subtotal.toFixed(2));
        $('#discount_total').val(discount.toFixed(2));
        $('#tax_total').val(tax.toFixed(2));
        $('#total_amount').val(total.toFixed(2));
    }

    // --- Khởi tạo lần đầu ---
    initSelect2($('.select2-product'));
});

////////////////////
// THEM MOI KHACH HANG
// Mở modal 2

// Submit form trong modal
/* $(document).on('hidden.bs.modal', '#ajaxCrudModal2', function () {
    // Khi modal 2 (thêm khách hàng) đóng lại, reload dữ liệu Select2 khách hàng
    $.ajax({
        url: '/customers/default/list', // 🔹 action trả JSON danh sách khách hàng
        type: 'GET',
        success: function (data) {
            alert(data);
            let $select = $('#invoiceform-customer_id'); // ID field Select2
            $select.empty();
            $.each(data, function (id, text) {
                let newOption = new Option(text, id, false, false);
                $select.append(newOption);
            });
            $select.trigger('change.select2');
        }
    });
}); */

// DONG MODAL THEM KH
$(document).on('hidden.bs.modal', '#ajaxCrudModal2', function () {
    // Khi modal 2 (thêm khách hàng) đóng lại, reload dữ liệu Select2 khách hàng
    $.ajax({
        url: '/customers/default/list', // 🔹 action trả JSON danh sách khách hàng
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            let $select = $('#invoiceform-customer_id'); // ID field Select2

            // Xóa tất cả option cũ
            $select.empty();

            // Thêm lại danh sách mới
            $.each(res.items, function (id, html) {
                //let newOption = new Option($(html).text(), id, false, false);
                let newOption = new Option(html, id, false, false);
                $select.append(newOption);
            });

            // Gán selected là khách hàng mới nhất
            if (res.maxId) {
                $select.val(res.maxId).trigger('change');
            } else {
                $select.val(null).trigger('change');
            }

            // Gọi lại Select2 để refresh UI
            $select.trigger('change.select2');
        },
        error: function (xhr) {
            console.error('Lỗi khi tải danh sách khách hàng:', xhr.responseText);
        }
    });
});



</script>
