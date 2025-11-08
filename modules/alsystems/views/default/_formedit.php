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
                //'initValueText' => $model->brand, // ✅ Bắt buộc thêm
                'options' => ['placeholder'=>'-- Chọn thương hiệu --','class'=>'select2-modal'],
                'pluginOptions' => [
                    'tags'=>true,
                    'tokenSeparators'=>[',',' '],
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
                //'initValueText' => $model->origin, // ✅ thêm dòng này
                'options'=>['placeholder'=>'-- Chọn xuất xứ --','class'=>'select2-modal'],
                'pluginOptions'=>[
                    'tags'=>true,
                    'tokenSeparators'=>[',',' '],
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
        <div class="col-md-3">
            <?= $form->field($model,'color')->dropDownList([
                'Ghi'=>'Ghi','Trắng sứ'=>'Trắng sứ','Đen'=>'Đen','Nâu cà phê'=>'Nâu cà phê',
                'Xanh dương'=>'Xanh dương','Xanh lá'=>'Xanh lá','Đỏ'=>'Đỏ','Vàng'=>'Vàng'
            ],['prompt'=>'-- Chọn màu --']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model,'surface_type')->dropDownList([
                'Sơn tĩnh điện'=>'Sơn tĩnh điện','Anod'=>'Anod','Phủ bóng'=>'Phủ bóng','Vân gỗ'=>'Vân gỗ'
            ],['prompt'=>'-- Chọn loại bề mặt --']) ?>
        </div>
        <div class="col-md-3">          
            <?php
                // Chuẩn hoá status trước khi render modal
                $val = strtolower(trim((string)$model->status));
                $model->status = in_array($val, ['1', 'active', 'true', 'yes', 'on'], true) ? 1 : 0; 

                echo $form->field($model, 'status')->widget(\kartik\switchinput\SwitchInput::class, [
                    'type' => \kartik\switchinput\SwitchInput::CHECKBOX,
                    'pluginOptions' => [
                        'onText' => 'Đang sử dụng',
                        'offText' => 'Không sử dụng',
                        'onColor' => 'success',
                        'offColor' => 'danger',
                        'size' => 'small',
                        'handleWidth' => 100,
                        'labelWidth' => 100,
                        'onValue' => 1,
                        'offValue' => 0,
                    ],
                    'options' => ['class' => 'mt-3'],
                ]); 
            ?>
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
                    <th>Ảnh</th>
                    <th>Trạng thái</th>
                    <th>Ghi chú</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($profiles as $i => $alProfiles): ?>   
                    <tr>
                        <td>
                            <?= Html::activeHiddenInput($alProfiles, "[$i]id", ['name'=>"alProfiles[$i][id]"]) ?>
                            <span class="stt-display"><?= $i + 1 ?></span>
                        </td>
                        <td><?= Html::activeTextInput($alProfiles, "[$i]code", [
                             'name' => "alProfiles[$i][code]",
                            'class' => 'form-control form-control-sm',
                        ]) ?></td>
                        <td><?= Html::activeTextInput($alProfiles, "[$i]name", [
                            'name' => "alProfiles[$i][name]",
                            'class' => 'form-control form-control-sm',
                        ]) ?></td>
                        <td><?= Html::activeTextInput($alProfiles, "[$i]door_types", [
                            'name' => "alProfiles[$i][door_types]",
                            'class' => 'form-control form-control-sm',
                        ]) ?></td>                        
                        <td>
                            <div class="input-group input-group-sm price-group">
                                <?= Html::activeTextInput($alProfiles, "[$i]length", [
                                    'name' => "alProfiles[$i][length]",
                                    'class' => 'form-control text-end price-input track-change',
                                    'autocomplete' => 'off',
                                    'placeholder' => '0',
                                    'value' => Yii::$app->formatter->asDecimal($alProfiles->length),
                                ]) ?>
                                <span class="input-group-text">mm</span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm price-group">
                                <?= Html::activeTextInput($alProfiles, "[$i]weight_per_meter", [
                                    'name' => "alProfiles[$i][weight_per_meter]",
                                    'class' => 'form-control text-end price-input track-change',
                                    'autocomplete' => 'off',
                                    'placeholder' => '0',
                                    'value' => Yii::$app->formatter->asDecimal($alProfiles->weight_per_meter),
                                ]) ?>
                                <span class="input-group-text">g/m</span>
                            </div>
                        </td>
                         <td>
                            <div class="input-group input-group-sm price-group">
                                <?= Html::activeTextInput($alProfiles, "[$i]unit_price", [
                                    'name' => "alProfiles[$i][unit_price]",
                                    'class' => 'form-control text-end price-input track-change',
                                    'autocomplete' => 'off',
                                    'placeholder' => '0',
                                    'value' => Yii::$app->formatter->asDecimal($alProfiles->unit_price),
                                ]) ?>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </td>  
                        <td>
                           <?= Html::activeTextInput($alProfiles, "[$i]image_url", [
                                'name' => "alProfiles[$i][image_url]",
                                'class' => 'form-control form-control-sm image-tooltip',
                                'data-bs-toggle' => 'tooltip',
                                'data-bs-html' => 'true', // Cho phép hiển thị HTML trong tooltip
                                'title' => $alProfiles->image_url 
                                    ? Html::img(Yii::getAlias('@web') . '/' . ltrim($alProfiles->image_url, '/'), [
                                        'style' => 'max-width:200px;max-height:200px;border-radius:8px;',
                                    ])
                                    : 'Chưa có hình',
                            ]) ?>
                        </td>          
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <?= Html::activeCheckbox($alProfiles, "[$i]status", [
                                    'name' => "alProfiles[$i][status]",
                                    'class' => 'form-check-input',
                                    'value' => 'active',
                                    'label' => false,
                                ]) ?>
                            </div>
                        </td>
                         <!-- Ghi chú -->
                        <td>
                            <?= Html::activeTextInput($alProfiles, "[$i]note", [
                                'name' => "alProfiles[$i][note]",
                                'class' => 'form-control note'
                            ]) ?>
                        </td>
                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-item">🗑</button></td>
                    </tr>                
                <?php endforeach;  ?>
            </tbody>
        </table>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs('
$(document).on("shown.bs.modal", "#ajaxCrudModal", function(){
    let $modal = $(this);

    // --- Khởi tạo tooltip ---
    $modal.find("[data-bs-toggle=\'tooltip\']").each(function(){
        if(!$(this).data("bs.tooltip")){
            new bootstrap.Tooltip(this, {
                html: true,
                sanitize: false,
                container: "#ajaxCrudModal"
            });
        }
    });

    // --- Khởi tạo Select2 ---
    $modal.find(".select2-modal").each(function(){
        if(!$(this).hasClass("select2-hidden-accessible")){
            $(this).select2({
                tags: true,
                tokenSeparators: [","," "],
                dropdownParent: $modal,
                allowClear: true
            });
        }
    });

    // --- Biến rowIndex lưu trong modal ---
    if(!$modal.data("rowIndex")){
        $modal.data("rowIndex", $("#system-details-table tbody tr").length);
    }

    // --- Thêm dòng mới ---
    $modal.find("#add-item-btn").off("click").on("click", function(){
        let rowIndex = $modal.data("rowIndex") + 1;
        $modal.data("rowIndex", rowIndex);

        let newRow = `<tr>
            <td class="stt-display"><span>${rowIndex}</span></td>
            <td><input type="text" name="alProfiles[${rowIndex}][code]" class="form-control form-control-sm"></td>
            <td><input type="text" name="alProfiles[${rowIndex}][name]" class="form-control form-control-sm"></td>
            <td><input type="text" name="alProfiles[${rowIndex}][door_types]" class="form-control form-control-sm"></td>
            <td>
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowIndex}][length]" class="form-control text-end price-input" placeholder="0">
                    <span class="input-group-text">mm</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowIndex}][weight_per_meter]" class="form-control text-end price-input" placeholder="0">
                    <span class="input-group-text">g/m</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm price-group">
                    <input type="text" name="alProfiles[${rowIndex}][unit_price]" class="form-control text-end price-input" placeholder="0">
                    <span class="input-group-text">VNĐ</span>
                </div>
            </td>
            <td><input type="text" name="alProfiles[${rowIndex}][image_url]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" name="alProfiles[${rowIndex}][status]" value="active" checked>
                </div>
            </td>
            <td><input type="text" name="alProfiles[${rowIndex}][note]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">🗑</button>
            </td>
        </tr>`;
        $("#system-details-table tbody").append(newRow);
    });

    // --- Xóa dòng ---
    $modal.off("click", ".remove-item").on("click", ".remove-item", function(){
        $(this).closest("tr").remove();
        $("#system-details-table tbody tr").each(function(i){
            $(this).find("td.stt-display span").text(i+1);
        });
    });

    // --- Định dạng giá tiền ---
    $modal.off("input", ".price-input").on("input", ".price-input", function(){
        let input = $(this);
        let cursorPos = input.prop("selectionStart");
        let beforeLength = input.val().length;

        let value = input.val();
        value = value.replace(/\D/g,"").replace(/\B(?=(\d{3})+(?!\d))/g,".");
        input.val(value);

        let afterLength = input.val().length;
        let newPos = cursorPos + (afterLength - beforeLength);
        input[0].setSelectionRange(newPos, newPos);
    });
});
');
?>
