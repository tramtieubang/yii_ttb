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

<div class="product-prices-unit-form-form">

    <?php $form = ActiveForm::begin(); ?>
	
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
				'placeholder' => 'Danh mục sản phẩm...',
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
