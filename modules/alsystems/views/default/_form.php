<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
?>

<style>
.system-form {
    background: #f7f9fb;
    padding: 5px 20px 20px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.system-section {
    background: #fff;
    border: 1px solid #e4e7eb;
    border-radius: 8px;
    margin-bottom: 14px;
    padding: 18px 20px;
}
.system-section h5 {
    font-size: 15px;
    font-weight: 600;
    color: #444;
    margin-bottom: 14px;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
.form-group { margin-bottom: 10px !important; }
.form-control, .select2-selection--single { 
    height: 34px !important; 
    padding: 4px 8px !important; 
    font-size: 13px; 
}
.control-label { font-size: 13px; font-weight: 500; color: #555; }
.table thead th { background-color: #f8fafc; color: #333; font-weight: 600; font-size: 13px; padding: 8px; }
.table td { padding: 6px; font-size: 13px; }
.btn-save { background: #28a745; border: none; color: white; font-weight: 500; padding: 8px 22px; border-radius: 6px; transition: 0.2s; }
.btn-save:hover { background: #218838; }
.btn-cancel { border: 1px solid #ccc; color: #555; background: #f8f9fa; font-weight: 500; padding: 8px 20px; border-radius: 6px; margin-left: 6px; transition: 0.2s; }
.btn-cancel:hover { background: #e9ecef; }
.select2-container--open { z-index: 99999 !important; }
.select2-dropdown { z-index: 99999 !important; }

/* Select2 nhỏ lại */
.select2-container--krajee .select2-selection {
    min-height: 32px !important;
}

/* Làm chữ trong input giá tiền to hơn, VNĐ nhỏ hơn */
.price-group .price-input {
    font-size: 13px !important;   /* chữ giá to hơn */
    /* font-weight: 600; */
    height: 30px;
}

.price-group .input-group-text {
    font-size: 11px !important;   /* chữ VNĐ nhỏ hơn */
    font-weight: 500;
    background-color: #f8f9fa;
    border-left: none;
    color: #666;
}
</style>

<div class="system-form">
<?php $form = ActiveForm::begin(); ?>
<!-- Danh mục hệ nhôm -->
<div class="system-section">
    <h5><i class="fas fa-cubes text-primary me-2"></i>Danh mục hệ nhôm</h5>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'code')->textInput(['maxlength'=>true,'placeholder'=>'VD: XF TP PMA']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'name')->textInput(['maxlength'=>true,'placeholder'=>'VD: Xingfa 55 - Topal 50 - PMA 60']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'brand')->widget(Select2::classname(), [
                'data' => [
                    'Xingfa'=>'🏠 Xingfa','PMA'=>'🔹 PMA','Topal'=>'⚡ Topal','Hopo'=>'🔧 Hopo',
                    'Kinlong'=>'🛠️ Kinlong','Eurowindow'=>'🌍 Eurowindow','Hafele'=>'🔑 Hafele',
                    'YKK'=>'🧵 YKK','VietPhap'=>'🇻🇳🇫🇷 VietPhap','Tostem'=>'🏗️ Tostem',
                ],
                'options' => ['placeholder'=>'-- Chọn thương hiệu --'],
                'pluginOptions' => [
                    'tags'=>true,
                    'tokenSeparators'=>[',',' '],
                    'dropdownParent'=> new JsExpression('document.body'),
                    'width'=>'100%',
                    'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
                    'dropdownParent' => new JsExpression("$('#ajaxCrudModal .modal-body')"),
                    'allowClear' => true,
                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'origin')->widget(Select2::classname(), [
                'data' => [
                    'Việt Nam'=>'🇻🇳 Việt Nam','Trung Quốc'=>'🇨🇳 Trung Quốc','Malaysia'=>'🇲🇾 Malaysia',
                    'Nhật Bản'=>'🇯🇵 Nhật Bản','Hàn Quốc'=>'🇰🇷 Hàn Quốc','Đức'=>'🇩🇪 Đức',
                    'Pháp'=>'🇫🇷 Pháp','Mỹ'=>'🇺🇸 Mỹ','Thái Lan'=>'🇹🇭 Thái Lan',
                    'Ấn Độ'=>'🇮🇳 Ấn Độ','Indonesia'=>'🇮🇩 Indonesia','Singapore'=>'🇸🇬 Singapore',
                ],
                'options'=>['placeholder'=>'-- Chọn xuất xứ --'],
                'pluginOptions'=>[
                    'tags'=>true,
                    'tokenSeparators'=>[',',' '],
                    'width'=>'100%',
                    'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
                    'dropdownParent' => new JsExpression("$('#ajaxCrudModal .modal-body')"),
                    'allowClear' => true,
                ],

            ]) ?>
        </div>
    </div>

    <div class="row">        
         <div class="col-md-3">
            <?= $form->field($model,'thickness')->input('number',['step'=>'0.01','min'=>0,'placeholder'=>'VD: 1.2, 2.5']) ?>
        </div>         
        <div class="col-md-3"><?= $form->field($model,'color')->dropDownList([
            'Ghi'=>'Ghi','Trắng sứ'=>'Trắng sứ','Đen'=>'Đen','Nâu cà phê'=>'Nâu cà phê',
            'Xanh dương'=>'Xanh dương','Xanh lá'=>'Xanh lá','Đỏ'=>'Đỏ','Vàng'=>'Vàng'
        ],['prompt'=>'-- Chọn màu --']) ?></div>
        <div class="col-md-3"><?= $form->field($model,'surface_type')->dropDownList([
            'Sơn tĩnh điện'=>'Sơn tĩnh điện','Anod'=>'Anod','Phủ bóng'=>'Phủ bóng','Vân gỗ'=>'Vân gỗ'
        ],['prompt'=>'-- Chọn loại bề mặt --']) ?></div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->widget(\kartik\switchinput\SwitchInput::class, [
				'type' => \kartik\switchinput\SwitchInput::CHECKBOX,
				'pluginOptions' => [
					'onText' => 'Đang sử dụng',
					'offText' => 'Không sử dụng',
					'onColor' => 'success',
					'offColor' => 'danger',
					'size' => 'small',
					'handleWidth' => 100,
					'labelWidth' => 100,
					'onValue' => 'Active',    // ✅ Khi bật → lưu "Active"
					'offValue' => 'Inactive', // ✅ Khi tắt → lưu "Inactive"
				],
				'options' => ['class' => 'mt-3'],
			]); ?>           
        </div>
    </div>

    <div class="row">
        <div class="col-md-12"><?= $form->field($model,'description')->textarea(['rows'=>2,'placeholder'=>'Mô tả chi tiết hệ nhôm...']) ?></div>
    </div>
</div>

<!-- Danh mục thanh nhôm -->
<div class="system-section">
    <div class="d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-cubes text-success me-2"></i>Danh mục thanh nhôm thuộc hệ</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn">➕ Thêm dòng</button>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm" id="system-details-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Tên chi tiết</th>
                    <th>Loại cửa</th>
                    <th class='text-center'>Chiều dài</th>
                    <th class='text-center'>Trọng lượng</th>
                    <th class='text-center'>Đơn giá</th> 
                    <th class='text-center'>Tổng</th>
                    <th>Ảnh</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <input type="text" name="Total" value=0 class="form-control form-control-sm text-end price" readonly>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>

<script>
$(document).ready(function(){
    // Thêm dòng mới
    $('#add-item-btn').on('click', function(){
        let rowCount = $('#system-details-table tbody tr').length + 1;
        let newRow = `<tr>
            <td>${rowCount}</td>
            <td><input type="text" name="alProfiles[${rowCount}][code]" class="form-control form-control-sm"></td>
            <td><input type="text" name="alProfiles[${rowCount}][name]" class="form-control form-control-sm"></td>
            <td><input type="text" name="alProfiles[${rowCount}][door_types]" class="form-control form-control-sm"></td>
            <td>
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowCount}][length]" 
                        class="form-control text-end length-input track-change" 
                        autocomplete="off" placeholder="0">
                    <span class="input-group-text">mm</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowCount}][weight_per_meter]" 
                        class="form-control text-end price-input track-change" 
                        autocomplete="off" placeholder="0,00">
                    <span class="input-group-text">g/m</span>
                </div>
            </td>
            <td style="width:140px;">
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowCount}][unit_price]" 
                        class="form-control text-end price-input track-change" 
                        autocomplete="off" placeholder="0,00">
                    <span class="input-group-text">VNĐ</span>
                </div>
            </td>
             <!-- ✅ CỘT THÀNH TIỀN -->
            <td style="width:140px;">
                <input type="text" name="alProfiles[${rowCount}][Total]" 
                    value="0,00" 
                    class="form-control form-control-sm text-end price" 
                    readonly>
            </td>
            <td><input type="text" name="alProfiles[${rowCount}][image_url]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" name="alProfiles[${rowCount}][status]" value="active" checked>
                </div>
            </td>
            <td><input type="text" name="alProfiles[${rowCount}][note]" class="form-control form-control-sm"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-item">🗑</button></td>
        </tr>`;
        $('#system-details-table tbody').append(newRow);
        
    });

    // Xóa dòng
    $(document).on('click', '.remove-item', function(){
        $(this).closest('tr').remove();
        renumberRows();
    });

    // Đánh lại STT
    function renumberRows(){
        $('#system-details-table tbody tr').each(function(i){
            $(this).find('td:first').text(i+1);
        });
    }

    // ======== HÀM ĐỊNH DẠNG ========

    // Khi người dùng thay đổi giá tri
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

    // Gán giá trị ban đầu sau khi thêm dòng mới
    $(document).on('focus', '.track-change', function() {
        if (!$(this).data('original')) {
            $(this).data('original', $(this).val());
        }
    });

    // Định dạng VNĐ: 000.000,00
    function formatCurrency(value) {
        if(!value) return '0,00';
        value = value.replace(/[^\d,]/g, '');
        const firstComma = value.indexOf(',');
        if(firstComma !== -1){
            let parts = [value.substring(0, firstComma), value.substring(firstComma + 1).replace(/,/g, '')];
            value = parts[0] + ',' + parts[1];
        }
        let [intPart, decPart] = value.split(',');
        intPart = intPart || '0';
        decPart = (decPart || '').substring(0, 2);
        if(decPart.length === 0) decPart = '00';
        else if(decPart.length === 1) decPart += '0';
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return intPart + ',' + decPart;
    }

    // Định dạng số nguyên có dấu chấm ngăn cách
    function formatInteger(value) {
        value = value.replace(/\D/g, '');
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // ======== SỰ KIỆN ========

    // Khi nhập cho trường giá, đơn giá, trọng lượng
    $(document).on('input', '.price-input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9,]/g, '');
        const firstComma = value.indexOf(',');
        if(firstComma !== -1){
            let intPart = value.substring(0, firstComma);
            let decPart = value.substring(firstComma + 1).replace(/,/g, '').substring(0, 2);
            value = intPart + (decPart ? ',' + decPart : ',' );
        }
        $(this).val(value);
    });

    // Khi nhập chiều dài → tự thêm dấu chấm real-time
    $(document).on('input', '.length-input', function() {
        let value = $(this).val();
        const cursorPosition = this.selectionStart;
        const beforeLength = value.length;

        // Loại bỏ mọi ký tự không phải số
        value = value.replace(/\D/g, '');
        // Thêm dấu chấm ngăn cách nghìn
        const formatted = formatInteger(value);

        $(this).val(formatted);

        // Giữ vị trí con trỏ hợp lý khi gõ
        const diff = formatted.length - beforeLength;
        this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
    });

    // Khi blur chiều dài
    $(document).on('blur', '.length-input', function() {
        $(this).val(formatInteger($(this).val()));
    });

    // Khi blur giá / đơn giá
    $(document).on('blur', '.price-input', function() {
        $(this).val(formatCurrency($(this).val()));
    });

    // TONG
    // ======== TÍNH TỔNG ========

    // ======== TÍNH TỔNG CHO MỖI DÒNG & TOÀN BẢNG ========

// Chuyển "1.234,56" => 1234.56
function parseToFloat(value) {
    if (!value) return 0;
    value = value.toString().replace(/\./g, '').replace(',', '.');
    const num = parseFloat(value);
    return isNaN(num) ? 0 : num;
}

// Định dạng lại về "1.234,56"
function formatCurrency(value) {
    if(!value) return '0,00';
    value = value.replace(/[^\d,]/g, '');
    const firstComma = value.indexOf(',');
    if(firstComma !== -1){
        let parts = [value.substring(0, firstComma), value.substring(firstComma + 1).replace(/,/g, '')];
        value = parts[0] + ',' + parts[1];
    }
    let [intPart, decPart] = value.split(',');
    intPart = intPart || '0';
    decPart = (decPart || '').substring(0, 2);
    if(decPart.length === 0) decPart = '00';
    else if(decPart.length === 1) decPart += '0';
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return intPart + ',' + decPart;
}

// 👉 Tính thành tiền cho từng dòng
function calculateRowTotal(row) {
    const weight = parseToFloat(row.find('input[name*="[weight_per_meter]"]').val());
    const price = parseToFloat(row.find('input[name*="[unit_price]"]').val());
    const total = weight + price;
    
    // Gán vào input alProfiles[${rowCount}][Total]
    row.find('input[name*="[Total]"]').val(formatCurrency(total.toFixed(2).toString().replace('.', ',')));
}

// 👉 Cập nhật tổng cuối bảng (ô Total)
function updateGrandTotal() {
    let sum = 0;
    $('#system-details-table tbody tr').each(function(){
        sum += parseToFloat($(this).find('input[name*="[Total]"]').val());
    });
    $('input[name="Total"]').val(formatCurrency(sum.toFixed(2).toString().replace('.', ',')));
}

// 👉 Khi người dùng nhập hoặc blur
$(document).on('input blur', 'input[name*="[weight_per_meter]"], input[name*="[unit_price]"]', function(){
    const row = $(this).closest('tr');
    calculateRowTotal(row);
    updateGrandTotal();
});

// 👉 Khi xóa dòng thì tính lại tổng
$(document).on('click', '.remove-item', function(){
    $(this).closest('tr').remove();
    renumberRows();
    updateGrandTotal();
});

        
});
</script>
