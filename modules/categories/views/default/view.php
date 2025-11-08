<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\categories\models\CategoriesForm */
?>
<div class="categories-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
