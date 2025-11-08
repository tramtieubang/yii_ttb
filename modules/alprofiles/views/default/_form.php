<?php

use app\modules\alsystems\models\AlSystemsForm;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\modules\alprofiles\models\AlProfilesForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="al-profiles-form-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
		<div class="col-md-4">    			
			<?php

				// Giả sử bạn có biến $system_id lấy từ controller
				$model->system_id = $system_id ?? null; // set selected trước khi hiển thị

				echo $form->field($model, 'system_id')->widget(Select2::classname(), [
					'data' => ArrayHelper::map(
						AlSystemsForm::find()->all(), 
						'id', 
						function($m) {
							return '<div style="display: flex;">
										<div style="width: 30px;">'.$m->code.'</div> 
										<div style="width: 150px;">'.$m->name.'</div>
										<div style="width: 100px;">'.$m->origin.'</div>
										<div>'.$m->color.'</div>
									</div>';
						}
					),
					'options' => [
						'placeholder' => 'Chọn hệ nhôm...',
						'disabled' => true, // 🔒 KHÔNG CHO CHỌN KHÁC
					],
					'pluginOptions' => [
						'escapeMarkup' => new JsExpression('function(markup) { return markup; }'),
						'dropdownParent' => new JsExpression("$('#ajaxCrudModal3 .modal-body')"),
						'allowClear' => false,
					],
				]);
			?>

		</div>
		<div class="col-md-4">    
			<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">    
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">    
			<?= $form->field($model, 'door_types')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-2">    
			<?php //$form->field($model, 'length')->textInput() 
				echo $form->field($model, 'length', [
					'enableClientValidation' => false, // ✅ tránh validate khi blur
					])->widget(MaskedInput::class, [
					'clientOptions' => [
						'alias' => 'numeric',
						'groupSeparator' => '.',
						'radixPoint' => ',',
						'autoGroup' => true,
						'digits' => 0,
						'suffix' => ' mm',
						'placeholder' => '0',
						'rightAlign' => true,
						'removeMaskOnSubmit' => true, // ✅ tự động gửi về giá trị 6000
					],
					'options' => [
						'class' => 'form-control',
						'autocomplete' => 'off',
						'onblur' => "this.value = this.value.replace(' mm','').replaceAll('.','').replace(',','').trim();",
					],
				]);
			?>
		</div>
		<div class="col-md-2">    
			<?php //$form->field($model, 'weight_per_meter')->textInput(['maxlength' => true]) 
				echo $form->field($model, 'weight_per_meter', [
					'enableClientValidation' => false, // ✅ tránh validate khi blur
				])->widget(MaskedInput::class, [
					'clientOptions' => [
						'alias' => 'numeric',
						'groupSeparator' => '.',
						'radixPoint' => ',',
						'autoGroup' => true,
						'digits' => 0,
						'suffix' => ' gm',
						'placeholder' => '0',
						'rightAlign' => true,
						'removeMaskOnSubmit' => true, // ✅ tự động gửi về giá trị 6000
					],
					'options' => [
						'class' => 'form-control',
						'autocomplete' => 'off',
						'onblur' => "this.value = this.value.replace(' gm','').replaceAll('.','').replace(',','').trim();",
					],
				]);
			?>
		</div>
		<div class="col-md-2">    
			<?php //$form->field($model, 'unit_price')->textInput(['maxlength' => true]) 
				echo $form->field($model, 'unit_price', [
					'enableClientValidation' => false,
				])->widget(MaskedInput::class, [
					'clientOptions' => [
						'alias' => 'numeric',
						'groupSeparator' => '.',
						'radixPoint' => ',',
						'autoGroup' => true,
						'digits' => 0,
						'suffix' => ' VNĐ',
						'placeholder' => '0',
						'rightAlign' => true,
						'removeMaskOnSubmit' => true,
					],
					'options' => [
						'class' => 'form-control',
						'autocomplete' => 'off',
						'onblur' => "this.value = this.value.replace(' VNĐ','').replaceAll('.','').replace(',','').trim();",
					],
				]); 
			?>
		</div>
		<div class="col-md-2">
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
						'handleWidth' => 60,
						'labelWidth' => 40,
                        'onValue' => 1,
                        'offValue' => 0,
                    ],
                    'options' => ['class' => 'mt-3'],
                ]); 
            ?>
			
		</div>
		<div class="col-md-6">
			<?= $form->field($model, 'image_url')->widget(FileInput::class, [
				'options' => [
					'accept' => 'image/*',
				],
				'pluginOptions' => [
					'showUpload' => false,
					'showCaption' => true,
					'showRemove' => true,
					'browseLabel' => 'Chọn hình...',
					'removeLabel' => 'Xóa',
					'initialPreview' => $model->image_url ? [Yii::getAlias('@web') . '/' . $model->image_url] : [],
					'initialPreviewAsData' => true,
					'initialPreviewConfig' => $model->image_url ? [['caption' => basename($model->image_url)]] : [],
					'overwriteInitial' => true,
					'maxFileSize' => 2048, // giới hạn 2MB
				],
			]); ?>
		</div>		
		<div class="col-md-6">    
			<?= $form->field($model, 'note')->textarea(['rows' => 16]) ?>
		</div>  
	</div>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Thêm mới' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
