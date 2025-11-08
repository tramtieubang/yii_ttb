<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\products\models\ProductsForm */
?>
<div class="products-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category_id',
            'name',
            'price',
            'datetime',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>


