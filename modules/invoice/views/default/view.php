<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<style>
.invoice-container {
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    font-family: "DejaVu Sans", "Helvetica", Arial, sans-serif;
    color: #333;
    font-family: 'Times New Roman', Times, serif;
}

/* Header */
.invoice-header {
    /* border-bottom: 2px solid #007bff; */
    padding-bottom: 0px;
    margin-bottom: 25px;
}

.invoice-header h2 {
    font-weight: 700;
    color: #007bff;
}

.invoice-info p {
    margin-bottom: 4px;
    font-size: 16px;
}

/* Table */
.table-invoice th {
    background-color: #f8f9fa;
    text-transform: uppercase;
    font-size: 15px;
}

.table-invoice td, .table-invoice th {
    vertical-align: middle;
    font-size: 15px;
}

/* Tổng cộng */
.invoice-summary {
    border-top: 2px solid #007bff;
    padding-top: 10px;
    margin-top: 15px;
    font-size: 15px;
}

/* Nút hành động */
.btn-action {
    min-width: 130px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 10px;
    font-weight: 500;
    border-radius: 6px;
}

.invoice-sign p {
    margin: 0;
    line-height: 1.2; /* khoảng cách sát nhau */
}

/* --- In ra A4 thật --- */
@media print {
    /* Ẩn tất cả trừ #invoice-content và các con */
    body * {
        display: none !important;
    }

    #invoice-content, #invoice-content * {
        display: block !important; /* hiển thị div và tất cả con */
    }

    /* Định dạng #invoice-content chuẩn A4 */
    #invoice-content {
        position: absolute;
        top: 0;
        left: 50%;
        width: 210mm;           /* chiều ngang A4 */
        min-height: 297mm;      /* chiều cao A4 */
        padding: 15mm 20mm;     /* lề thực tế */
        box-sizing: border-box;
        transform: translateX(-50%); /* căn giữa ngang */
        background: #fff;
        color: #000;
        font-size: 13px;
    }

    /* Ẩn nút không cần in */
    .btn-action,
    .mb-4.d-flex {
        display: none !important;
    }

    /* Giữ bố cục 2 cột */
    .row {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
    .col-md-6 {
        width: 50% !important;
        float: left !important;
    }

    /* Chữ ký cách đều */
    .row.text-center .col-md-6 {
        text-align: center !important;
        width: 50% !important;
        display: inline-block !important;
    }

    .invoice-sign p {
        margin: 0;
        line-height: 1.2; /* khoảng cách sát nhau */
    }

}

</style>


<div class="container py-4">
    <!-- Nút hành động -->
    <div class="mb-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-primary btn-md btn-action" id="btn-print">
            <i class="fa fa-print"></i> In
        </button>
        <?= Html::a(
            '<i class="fas fa-file-excel"></i> Excel template', 
            ['/invoice/report/excel-report-template', 'id' => $model->id], [
            'class' => 'btn btn-outline-success btn-md btn-action',
            'title' => 'Xuất Excel',
        ]) ?>
        <?= Html::a(
            '<i class="fas fa-file-excel"></i> Excel', 
            ['/invoice/report/excel-report', 'id' => $model->id], [
            'class' => 'btn btn-outline-success btn-md btn-action',
            'title' => 'Xuất Excel',
        ]) ?>
        <?= Html::a(
            '<i class="fas fa-file-pdf"></i> PDF',
            ['/invoice/report/pdf-report', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-danger btn-md btn-action',
                'target' => '_blank',
                'title' => 'Xuất PDF',
            ]
        ) ?>
    </div>

    <!-- Nội dung hóa đơn -->
    <div id="invoice-content" class="invoice-container">
        
        <!-- Header -->
        <div class="invoice-header text-center">
            <h2>HÓA ĐƠN BÁN HÀNG</h2>
            <p class="text-muted mb-0">Số: <?= Html::encode($model->invoice_number) ?></p>
        </div>

        <!-- Thông tin chung -->
        <div class="row mb-4 invoice-info">
            <div class="col-md-6">
                <p><strong>Khách hàng:</strong> <?= Html::encode($model->customer->name ?? '') ?></p>
                <p><strong>Ngày lập:</strong> <?= Yii::$app->formatter->asDate($model->issue_date, 'php:d/m/Y') ?></p>
                <p><strong>Ngày đến hạn:</strong> <?= Yii::$app->formatter->asDate($model->due_date, 'php:d/m/Y') ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Phương thức thanh toán:</strong> <?= ucfirst($model->payment_method) ?></p>
                <p><strong>Trạng thái:</strong> 
                    <span class="badge bg-<?= $model->status == 'paid' ? 'success' : ($model->status == 'cancelled' ? 'secondary' : 'warning') ?>">
                        <?= ucfirst($model->status) ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Bảng chi tiết -->
        <table class="table table-bordered table-striped table-invoice" style="width: 100%;">
            <thead class="text-center">
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn vị tính</th>
                    <th class="text-end">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                    <th class="text-end">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; $subtotal = 0; foreach ($details as $d): ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><?= Html::encode($d->productPriceUnit->product->name ?? '') ?></td>
                        <td class="text-center"><?= Html::encode($d->productPriceUnit->unit->name ?? '') ?></td>
                        <td class="text-end"><?= $d->quantity ?></td>
                        <td class="text-end"><?= number_format($d->unit_price, 2, ',', '.') ?></td>
                        <td class="text-end"><?= number_format($d->total, 2, ',', '.') ?></td>
                        <td class="text-end"><?= $d->notes ?></td>
                    </tr>
                    <?php $subtotal += $d->total; endforeach; ?>
            </tbody>
        </table>

        <!-- Tổng kết -->
        <div class="row mt-4">
            <div class="col-md-6 order-2 order-md-1">
                <strong>Ghi chú:</strong>
                <p class="mt-2 mb-0"><?= nl2br(Html::encode($model->notes)) ?></p>
            </div>
            <div class="col-md-6 order-1 order-md-2">
                <div class="invoice-summary">
                    <table style="width:100%; border-collapse: collapse; font-family: 'Times New Roman', sans-serif; font-size:13px;">
                        <tr>
                            <td style="padding:2px 5px;">Tổng trước thuế:</td>
                            <td style="padding:2px 5px; text-align:right;"><?= number_format($subtotal, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:2px 5px;">Giảm giá:</td>
                            <td style="padding:2px 5px; text-align:right;"><?= number_format($model->discount_total ?? 0, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:2px 5px;">Thuế:</td>
                            <td style="padding:2px 5px; text-align:right;"><?= number_format($model->tax_total ?? 0, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-top:2px solid #000; height:5px;"></td>
                        </tr>
                        <tr style="font-weight:bold; font-size:15px; color:#0d6efd;">
                            <td>Tổng cộng:</td>
                            <td style="text-align:right;"><?= number_format($model->total_amount, 2, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <!-- Chữ ký -->
        <div class="row text-center mt-5">
            <div class="col-md-6 invoice-sign">
               <strong>Người lập hóa đơn</strong></br>
                Ký, ghi rõ họ tên
            </div>
            <div class="col-md-6 invoice-sign">
                <strong>Khách hàng</strong></br>
                (Ký, ghi rõ họ tên)
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS

/* $(document).off('click', '#btn-print').on('click', '#btn-print', function() {
    //window.print();
    printDiv('invoice-content');
}); */

document.getElementById('btn-print').addEventListener('click', function() {
    printDiv('invoice-content');
});

function printDiv(divId) {
    const content = document.getElementById(divId).innerHTML;
    const invoiceCSS = `
        <style>
            @page { size: A4; margin: 0; }
            body { margin:0; font-family: 'Times New Roman', Times, serif; color: #333; }
            #invoice-content { width: 210mm; min-height: 297mm; padding: 15mm 20mm; box-sizing: border-box; }
            .invoice-header { text-align: center; padding-bottom: 10px; margin-bottom: 0px; }
            .invoice-header h2 { color: #007bff; font-weight: 700; }
            .table-invoice { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .table-invoice th { background-color: #f8f9fa; text-transform: uppercase; font-size: 13px; }
            .table-invoice td, .table-invoice th { vertical-align: middle; font-size: 14px; border: 1px solid #dee2e6; padding: 5px; }
            .invoice-summary { border-top: 2px solid #007bff; padding-top: 10px; margin-top: 15px; font-size: 14px; }
            .row { display: flex; flex-wrap: nowrap; }
            .col-md-6 { width: 50%; float: left; }
            .text-center { text-align: center; }
        </style>
    `;

    const myWindow = window.open('', 'Print', 'width=800,height=600');
    myWindow.document.write('<html><head><title>In hóa đơn</title>');
    myWindow.document.write(invoiceCSS); // copy toàn bộ CSS
    myWindow.document.write('<style>@page { size: A4; margin: 0; } body { margin:0; }</style>');
    myWindow.document.write('</head><body>');
    myWindow.document.write(content);
    myWindow.document.write('</body></html>');
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

JS;
$this->registerJs($js);
?>
