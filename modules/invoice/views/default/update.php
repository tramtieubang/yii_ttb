<?php

use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\invoice\models\InvoiceForm */
?>
<div class="invoice-form-update">

    <?= $this->render('_formedit', [
        'model' => $model,
        'latestPrices' => $latestPrices,
        'details' => $details ?? [], // ✅ thêm dòng này
    ]) ?>

</div>
