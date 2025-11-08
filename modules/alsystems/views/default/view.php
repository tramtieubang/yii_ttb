<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\alsystemsprofiles\models\AlSystemsForm */
?>
<div class="al-systems-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'origin',
            'description:ntext',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
