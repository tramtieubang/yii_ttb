<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\alprofiles\models\AlProfilesForm */
?>
<div class="al-profiles-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'system_id',
            'code',
            'name',
            'door_types',
            'length',
            'weight_per_meter',
            'unit_price',
            'image_url:url',
            'note:ntext',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
