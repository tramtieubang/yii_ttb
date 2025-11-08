<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\alaluminummaterials\models\AlAluminumMaterialsForm */
?>
<div class="al-aluminum-materials-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'profile_id',
            'code',
            'name',
            'length',
            'stock_quantity',
            'stock_length',
            'unit_price',
            'note:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
