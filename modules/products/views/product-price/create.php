<?php

use app\modules\product_prices_unit\models\ProductPricesUnitForm;
use app\modules\products\models\ProductsForm;
use app\modules\units\models\UnitsForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\modules\product_prices_unit\models\ProductPricesUnitForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="save-product-price-form">

    <?php 
        /* $form = ActiveForm::begin([
            'id' => 'save-product-price-form',
            'enableAjaxValidation' => false,
            'options' => ['data-pjax' => true],
            'action' => ['/products/product-price/save'], // route xử lý POST
        ]);  */

        // Cach 2 dung ajax script
        $form = ActiveForm::begin([
            'id' => 'save-product-price-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => true, // ✅ vẫn cho kiểm tra client
            'options' => [
                'data-pjax' => true,
                'autocomplete' => 'off',
            ],
            'action' => ['/products/product-price/save'], // ✅ Controller lưu
        ]); 
    ?>
	
	<div class="row">
        <div class="col-md-12">    
            <?= //$form->field($model, 'product_id')->textInput() 
                $form->field($model, 'product_id')->widget(Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(
                        ProductsForm::find()->all(), 
                        'id', 
                        function($model) {
                            return '<div style="display: flex;">
                                        <div style="width: 30px;">'.$model->id.'</div> 
                                        <div>'.$model->name.'</div>
                                    </div>';
                        }
                    ),
                    'options' => [
                        'placeholder' => 'Danh mục sản phẩm...',
                        'value' => $productid,
                    ],
                    'pluginOptions' => [
                        'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
                        'dropdownParent' => new JsExpression("$('#ajaxCrudModal .modal-body')"),
                        'allowClear' => true,
                    ],
                ]);
            ?>

        </div><div class="col-md-4">    
            <?= //$form->field($model, 'unit_id')->textInput() 
                $form->field($model, 'unit_id')->widget(Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(
                        UnitsForm::find()->all(), 
                        'id', 
                        function($model) {
                            return '<div style="display: flex;">
                                        <div style="width: 30px;">'.$model->id.'</div> 
                                        <div>'.$model->name.'</div>
                                    </div>';
                        }
                    ),
                    'options' => [
                        'placeholder' => 'Danh mục đơn vị tính...',
                    ],
                    'pluginOptions' => [
                        'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
                        'dropdownParent' => new JsExpression("$('#ajaxCrudModal .modal-body')"),
                        'allowClear' => true,
                    ],
                ]);
            ?>

        </div><div class="col-md-4">    
            <?= //$form->field($model, 'price')->textInput(['maxlength' => true]) 
                $form->field($model, 'price')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'numeric',
                        'groupSeparator' => '.',
                        'radixPoint' => ',',
                        'autoGroup' => true,
                        'digits' => 0,
                        'digitsOptional' => false,
                        'prefix' => '', // nếu muốn ký hiệu tiền tệ trước
                        'suffix' => ' VNĐ', // ký hiệu VNĐ
                        'placeholder' => '0',
                        'rightAlign' => true,
                    ],
                ]);
            ?>

        </div><div class="col-md-4">    
        <?= //$form->field($model, 'datetime')->textInput() 
            $form->field($model, 'datetime')->widget(codenixsv\flatpickr\Flatpickr::class, [
                'clientOptions' => [
                    'enableTime'    => true,
                    'enableSeconds' => true,
                    'dateFormat'    => 'd/m/Y H:i:s',
                    'time_24hr'     => true,
                    'locale'        => 'vn',
                ],
                'options' => ['class' => 'form-control flatpickr-input'],
            ]);
        ?>

    </div>  
	</div>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Thêm mới' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<!-- 🔄 Loading Overlay -->
<div id="loading-overlay" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(255,255,255,0.7);
    z-index:2000;
    text-align:center;
    padding-top:20%;
">
    <div class="spinner-border text-primary" style="width:3rem; height:3rem;" role="status"></div>
    <div style="margin-top:10px; font-weight:600; color:#333;">Đang lưu dữ liệu...</div>
</div>


<script>
    $(document).ready(function(){
        // --- AJAX lưu lại ---
        $(document).on('click', '#btn-save-price', function(e) {
            e.preventDefault();

            const form = $('#save-product-price-form');

           if (!form.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không tìm thấy form để lưu!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
             
            // 🟧 Lấy dữ liệu form để kiểm tra trước khi gửi
            const product = form.find('#productpricesunitform-product_id').val();
            const unit = form.find('#productpricesunitform-unit_id').val();
            const price = form.find('#productpricesunitform-price').val();
            const datetime = form.find('#productpricesunitform-datetime').val();

            // 🟥 Kiểm tra dữ liệu rỗng
            if (!product) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu dữ liệu!',
                    text: 'Vui lòng chọn sản phẩm!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (!unit) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu dữ liệu!',
                    text: 'Vui lòng chọn đơn vị tính!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (!price || price.trim() === '' || price.trim() === '0 VNĐ') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu dữ liệu!',
                    text: 'Vui lòng nhập giá sản phẩm!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (!datetime) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu dữ liệu!',
                    text: 'Vui lòng nhập ngày giờ áp dụng giá!',
                    toast: true,
                    position: 'top-end',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // 🟦 Hiển thị loading
            $('#loading-overlay').fadeIn(200);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    // 🟩 Ẩn loading
                    $('#loading-overlay').fadeOut(1000);

                    if (response.success) {
                        // ✅ Reload GridView
                        if (response.forceReload) {
                            $.pjax.reload({container: response.forceReload});
                        }

                        // ✅ Reset form nhập liệu
                        /* if (response.resetForm) {
                            form.trigger('reset');
                            form.find('select').val(null).trigger('change');
                            form.find('input, textarea').removeClass('is-invalid');
                        } */

                        if (response.resetForm) {
                            // Giữ lại giá trị sản phẩm được chọn
                            const selectedProduct = form.find('#productpricesunitform-product_id').val();

                            // Reset toàn bộ form
                            form.trigger('reset');
                            form.find('input, textarea').removeClass('is-invalid');

                            // Giữ lại Select2 product_id
                            form.find('#productpricesunitform-product_id')
                                .val(selectedProduct)
                                .trigger('change.select2'); 

                            // Reset Select2 khác (unit_id) về rỗng
                            form.find('#productpricesunitform-unit_id')
                                .val(null)
                                .trigger('change.select2');

                            // Reset các input khác (nếu có)
                            form.find('#productpricesunitform-price').val('');
                            form.find('#productpricesunitform-datetime').val('');
                        }    

                         // ✅ Hiển thị popup thành công
                        /* Swal.fire({
                            title: 'Thành công!',
                            text: response.tcontent || 'Dữ liệu đã được lưu thành công.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 1800,
                            timerProgressBar: true
                        }); */

                        // ✅ Hiển thị thông báo SweetAlert ở góc phải trên
                        Swal.fire({
                            icon: 'success',
                            title: response.tcontent || 'Lưu thành công!',
                            toast: true,
                            position: 'top-end',   // 📍 Góc phải trên
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                         Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
                    }
                },
                error: function() {
                    // 🟥 Ẩn loading khi lỗi
                    $('#loading-overlay').fadeOut(200);
                    toastr.error('Lỗi kết nối khi lưu dữ liệu!', 'Lỗi mạng');
                }
            });
        });


    });

</script>