<?php

use yii\bootstrap5\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\alsystemsprofiles\models\AlSystemsForm */

?>
<div class="al-systems-form-create">
    <?= $this->render('_form', [
        'model' => $model,
        'profiles' => $profiles,
    ]) ?>
</div>
