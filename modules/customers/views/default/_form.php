<?php
use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\customers\models\CustomersForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customers-form-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
<div class="col-md-4">    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    
	<?= 
		$form->field($model, 'phone')->textInput([
			'maxlength' => true,
			'placeholder' => 'Nhập số điện thoại',
		])
	?>

</div><div class="col-md-12">    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-12">    
	<?= 
		$form->field($model, 'note')->textarea(['maxlength' => true,'row' => 3]) 
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
