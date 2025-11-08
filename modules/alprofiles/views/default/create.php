<?php

use yii\bootstrap5\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\alprofiles\models\AlProfilesForm */

?>
<div class="al-profiles-form-create">
    <?= $this->render('_form', [
        'model' => $model,
        'system_id' => $system_id ?? null, // ✅ thêm dòng này
    ]) ?>
</div>
