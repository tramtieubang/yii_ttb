<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\alsystemsprofiles\models\AlSystemsForm */
?>
<div class="al-systems-form-update">

    <?= $this->render('_formedit', [
        'model' => $model,
        'profiles' => $profiles,
    ]) ?>

</div>
