<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\modules\customers\models\CustomersForm;
use app\modules\products\models\ProductsForm;
use codenixsv\flatpickr\Flatpickr;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
    ]); ?>

    <!-- 🧾 Thông tin hóa đơn -->
    <div class="invoice-section">
        <h5><i class="fas fa-file-invoice text-success me-2"></i>Thông tin hóa đơn</h5>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'invoice_number')->textInput(['maxlength' => true, 'placeholder' => 'VD: HD-2025-001']) ?>
            </div>
            <div class="col-md-9">
               <?= //$form->field($model, 'category_id')->textInput() 
					$form->field($model, 'customer_id')->widget(Select2::classname(), [
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
                    ], ['' => 'Chọn phương thức...']) 
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
                <tbody>
                     <?php foreach ($details as $i => $detail): ?>
                        <tr>
                            <td><?= Html::activeHiddenInput($detail, "[$i]id") ?><?= $i + 1 ?></td>
                            <td>
                                <?= Select2::widget([
                                    'model' => $detail,
                                    'attribute' => "[$i]product_price_unit_id",
                                    'data' => ArrayHelper::map($latestPrices, 'id', 'text'),
                                    'options' => [
                                        'id' => "invoicedetail-{$i}-product_price_unit_id", // id chuẩn theo model
                                        'placeholder' => 'Chọn sản phẩm - đơn vị - giá...',
                                        'class' => 'form-control select-product',
                                        'data-row' => $i, // dùng để JS cập nhật đúng dòng
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '100%',
                                        'dropdownParent' => new \yii\web\JsExpression("$('#ajaxCrudModal .modal-body')"),
                                    ],
                                ]) ?>
                            </td>

                            <!-- Số lượng -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <?= Html::activeTextInput($detail, "[$i]quantity", [
                                        'class' => 'form-control text-center qty',
                                        'type' => 'number',
                                        'min' => 1,
                                        'step' => 1,
                                        'style' => 'max-width:80px;',
                                        'data-row' => $i,
                                    ]) ?>
                                </div>
                            </td>

                            <!-- Đơn giá -->
                            <td><?= Html::activeTextInput($detail, "[$i]unit_price", [
                                'class' => 'form-control text-end price',
                                'readonly' => true,
                                'value' => Yii::$app->formatter->asDecimal($detail->unit_price, 2),
                            ]) ?></td>

                            <!-- Thành tiền -->
                            <td><?= Html::activeTextInput($detail, "[$i]total", [
                                'class' => 'form-control text-end total',
                                'readonly' => true,
                                'value' => Yii::$app->formatter->asDecimal($detail->total, 2),
                            ]) ?></td>

                            <!-- Ghi chú -->
                            <td><?= Html::activeTextInput($detail, "[$i]notes", ['class' => 'form-control note']) ?></td>

                            <!-- Nút xóa dòng -->
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 💵 Tổng tiền -->
    <div class="invoice-section total-box text-end">
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-1">
                    <span>Tổng trước thuế:</span>
                    <strong id="subtotal-display">
                        <?= Yii::$app->formatter->asDecimal($model->subtotal, 2) ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Giảm giá:</span>
                    <strong id="discount-display">
                        <?= Yii::$app->formatter->asDecimal($model->discount_total, 2) ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Thuế:</span>
                    <strong id="tax-display">
                        <?= Yii::$app->formatter->asDecimal($model->tax_total, 2) ?>
                    </strong>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span><strong>Tổng cộng:</strong></span>
                    <h5 class="text-primary mb-0">
                        <strong id="total-display">
                            <?= Yii::$app->formatter->asDecimal($model->total_amount, 2) ?>
                        </strong>
                    </h5>
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
// --- Chuẩn bị dữ liệu sản phẩm (id, text, price) ---
$productOptions = [];
$latestPrices = ProductsForm::find()->with(['latestProductPricesUnit.unit'])->all();

foreach ($latestPrices as $p) {
    if (!empty($p->latestProductPricesUnit)) {
        foreach ($p->latestProductPricesUnit as $price) {
            $productOptions[] = [
                'id' => $price->id,
                'text' => "{$p->name} - {$price->unit->name} - " . number_format($price->price, 2, ',', '.'),
                'price' => $price->price
            ];
        }
    }
}

$productOptionsJson = Json::encode($productOptions);
?>

<?php
// --- Chuẩn bị dữ liệu sản phẩm (id, text, price) ---
$productOptions = [];
$products = ProductsForm::find()->with(['latestProductPricesUnit.unit'])->all();

foreach ($products as $p) {
    foreach ($p->latestProductPricesUnit as $price) {
        $productOptions[] = [
            'id' => $price->id,
            'text' => "{$p->name} - {$price->unit->name} - " . number_format($price->price, 2, ',', '.'),
            'price' => $price->price,
        ];
    }
}

$productOptionsJson = Json::encode($productOptions);
?>

<?php
$this->registerJs(<<<JS
// ==================== KHỞI TẠO DỮ LIỆU SẢN PHẨM ====================
if (typeof window.productOptions === 'undefined') {
    window.productOptions = $productOptionsJson;
}

// --- Hàm định dạng tiền ---
function formatCurrency(value) {
    if (!value) value = 0;
    return parseFloat(value).toLocaleString('vi-VN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// --- Bỏ định dạng để tính toán ---
function parseCurrency(str) {
    if (!str) return 0;
    return parseFloat(str.replace(/\\./g, '').replace(',', '.')) || 0;
}

// --- Khởi tạo Select2 ---
function initSelect2(el) {
    el.select2({
        width: '100%',
        data: window.productOptions,
        placeholder: 'Chọn sản phẩm - đơn vị - giá...',
        allowClear: true,
        dropdownParent: $('#ajaxCrudModal .modal-body')
    });
}

// --- Khi chọn sản phẩm ---
$(document).off('select2:select', '.select-product').on('select2:select', '.select-product', function() {
    const row = $(this).closest('tr');
    const selectedId = $(this).val();
    const product = window.productOptions.find(p => p.id == selectedId);

    if (product) {
        const price = parseFloat(product.price) || 0;
        const qty = parseFloat(row.find('.qty').val()) || 1;
        row.find('.price').val(formatCurrency(price));
        row.find('.total').val(formatCurrency(qty * price));
    } else {
        row.find('.price').val('');
        row.find('.total').val('');
    }
    updateTotals();
});

// --- Khi thay đổi số lượng ---
$(document).off('input', '.qty').on('input', '.qty', function() {
    const row = $(this).closest('tr');
    const qty = parseFloat($(this).val()) || 0;
    const price = parseCurrency(row.find('.price').val());
    row.find('.total').val(formatCurrency(qty * price));
    updateTotals();
});

// --- Khi thay đổi giảm giá hoặc thuế ---
$(document).off('input', '#discount_total, #tax_total').on('input', '#discount_total, #tax_total', function() {
    updateTotals();
});

// --- Thêm dòng mới ---
$(document).off('click', '#add-item-btn').on('click', '#add-item-btn', function() {
    const rowCount = $('#invoice-details-table tbody tr').length;

    const newRow = `
        <tr>
            <td>\${rowCount + 1}</td>
            <td>
                <select name="InvoiceDetail[\${rowCount}][product_price_unit_id]" 
                        class="form-control form-control-sm select-product">
                    <option></option>
                </select>
            </td>
            <td>
                <input type="number" name="InvoiceDetail[\${rowCount}][quantity]" 
                       class="form-control form-control-sm text-center qty" 
                       value="1" min="1">
            </td>
            <td>
                <input type="text" name="InvoiceDetail[\${rowCount}][unit_price]" 
                       class="form-control form-control-sm text-end price" readonly>
            </td>
            <td>
                <input type="text" name="InvoiceDetail[\${rowCount}][total]" 
                       class="form-control form-control-sm text-end total" readonly>
            </td>
            <td>
                <input type="text" name="InvoiceDetail[\${rowCount}][notes]" 
                       class="form-control form-control-sm note">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#invoice-details-table tbody').append(newRow);
    initSelect2($('#invoice-details-table tbody tr:last .select-product'));
});

// --- Xóa dòng ---
$(document).off('click', '.remove-item').on('click', '.remove-item', function() {
    $(this).closest('tr').remove();
    renumberRows();
    updateTotals();
});

// --- Đánh lại số thứ tự ---
function renumberRows() {
    $('#invoice-details-table tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

// --- Cập nhật tổng tiền ---
function updateTotals() {
    let subtotal = 0;
    $('.total').each(function() {
        subtotal += parseCurrency($(this).val());
    });

    const discount = parseCurrency($('#discount_total').val());
    const tax = parseCurrency($('#tax_total').val());
    const total = subtotal - discount + tax;

    $('#subtotal-display').text(formatCurrency(subtotal));
    $('#discount-display').text(formatCurrency(discount));
    $('#tax-display').text(formatCurrency(tax));
    $('#total-display').text(formatCurrency(total));

    $('#subtotal').val(subtotal.toFixed(2));
    $('#total_amount').val(total.toFixed(2));
}

// --- Khởi tạo ban đầu ---
$(document).ready(function() {
    initSelect2($('.select-product'));
    updateTotals();
});
JS);
?>
