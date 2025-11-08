<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\customers\models\CustomersForm */
?>
<div class="customers-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'email:email',
            'phone',
            'address',
            'note',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
