<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\units\models\UnitsForm */
?>
<div class="units-form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'note',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
