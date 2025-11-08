<?php

use app\custom\CustomFunc;
use app\modules\categories\models\CategoriesForm;
//use codenixsv\flatpickr\Flatpickr;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\modules\products\models\ProductsForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
<div class="col-md-12">    
	<?= //$form->field($model, 'category_id')->textInput() 
		$form->field($model, 'category_id')->widget(Select2::classname(), [
			'data' => \yii\helpers\ArrayHelper::map(
				CategoriesForm::find()->all(), 
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
		
</div><div class="col-md-12">    
	<?= 
		$form->field($model, 'name')->textInput(['maxlength' => true]) 
	?>

	</div>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Thêm mới' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

