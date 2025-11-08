<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\product_prices_unit\models\ProductPricesUnitForm */
?>
<div class="product-prices-unit-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'product_id',
            'unit_id',
            'price',
            'datetime',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
