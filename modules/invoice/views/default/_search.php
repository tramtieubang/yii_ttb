<?php
use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\invoice\models\InvoiceForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-form-search">

    <?php $form = ActiveForm::begin([
        	'id'=>'myFilterForm',
            'method' => 'get',
            'options' => [
                'class' => 'myFilterForm'
            ]
      	]); ?>
	<div class="row">
<div class="col-md-4">    <?= $form->field($model, 'invoice_number')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'customer_id')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'issue_date')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'due_date')->textInput() ?>

</div><div class="col-md-4">    <?= $form->field($model, 'subtotal')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'discount_total')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'tax_total')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'total_amount')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'status')->dropDownList([ 'draft' => 'Draft', 'unpaid' => 'Unpaid', 'paid' => 'Paid', 'cancelled' => 'Cancelled', ], ['prompt' => '']) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'payment_method')->textInput(['maxlength' => true]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

</div><div class="col-md-4">    <?= $form->field($model, 'created_by')->textInput() ?>

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