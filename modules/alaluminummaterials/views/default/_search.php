<?php
use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\alaluminummaterials\models\AlAluminumMaterialsForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="al-aluminum-materials-form-search">

    <?php $form = ActiveForm::begin([
        	'id'=>'myFilterForm',
            'method' => 'get',
            'options' => [
                'class' => 'myFilterForm'
            ]
      	]); ?>
	<div class="row">
<div class="col-md-4">    <?= $form->field($model, 'profile_id')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'length')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'stock_quantity')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'stock_length')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'unit_price')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'created_at')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'updated_at')->textInput() ?>

</div>  
	</div>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton('Tìm kiếm',['class' => 'btn btn-primary']) ?>
	        <?= Html::button('Xóa tìm kiếm', ['class' => 'btn btn-outline-secondary', 'onclick' => 'resetSearchForm()']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<script>
	function resetSearchForm() {
		const form = $('#myFilterForm');
		form.find('input[type="text"], select').val('');
		form.submit();
	}
</script>