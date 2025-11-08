<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php if (!empty($data)): ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-gradient text-white py-2"
         style="background: linear-gradient(90deg, #007bff, #00a6ff);">
        <strong><i class="fas fa-file-excel me-2"></i> Xem trước dữ liệu từ Excel</strong>
    </div>

    <div class="card-body p-3">
        <!-- Nút lưu phía trên -->
        <div class="mb-2 d-flex justify-content-end gap-2">
            <?= Html::button('<i class="fas fa-save me-1"></i> Lưu tất cả', [
                'class'=>'btn btn-success btn-sm fw-bold',
                'id'=>'save-all'
            ]) ?>
            <?= Html::button('<i class="fas fa-check me-1"></i> Lưu dòng chọn', [
                'class'=>'btn btn-primary btn-sm fw-bold',
                'id'=>'save-selected'
            ]) ?>
        </div>

        <div class="table-responsive">
            <form id="import-preview-form">
                <input type="hidden" id="excel-data" value='<?= json_encode($data) ?>'>

                <table id="preview-table" class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 3%; text-align:center;">
                                <input type="checkbox" id="check-all" title="Chọn tất cả">
                            </th>
                            <th style="width: 5%">ID</th>
                            <th style="width: 15%">Họ tên</th>
                            <th style="width: 20%">Email</th>
                            <th style="width: 15%">Điện thoại</th>
                            <th style="width: 25%">Địa chỉ</th>
                            <th style="width: 17%">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="rows[]" value="<?= $i ?>" class="row-check">
                            </td>
                            <td><?= Html::encode($i + 1) ?></td>
                            <td><?= Html::encode($row['name'] ?? $row['B'] ?? '') ?></td>
                            <td><?= Html::encode($row['email'] ?? $row['C'] ?? '') ?></td>
                            <td><?= Html::encode($row['phone'] ?? $row['D'] ?? '') ?></td>
                            <td><?= Html::encode($row['address'] ?? $row['E'] ?? '') ?></td>
                            <td><?= Html::encode($row['note'] ?? $row['F'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Nút lưu phía dưới -->
        <div class="mt-3 d-flex justify-content-end gap-2">
            <?= Html::button('<i class="fas fa-save me-1"></i> Lưu tất cả', [
                'class'=>'btn btn-success btn-sm fw-bold',
                'id'=>'save-all-bottom'
            ]) ?>
            <?= Html::button('<i class="fas fa-check me-1"></i> Lưu dòng chọn', [
                'class'=>'btn btn-primary btn-sm fw-bold',
                'id'=>'save-selected-bottom'
            ]) ?>
        </div>
    </div>
</div>

<style>
#preview-table th {
    background-color: #f8f9fa;
    color: #212529;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 8px 5px;
}
#preview-table td {
    padding: 8px 10px;
    font-size: 14px;
    vertical-align: middle;
}
#preview-table tr:hover {
    background-color: #f1f9ff;
}
#preview-table input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
</style>

<script>
$(function() {
    // ✅ Chọn tất cả
    $('#check-all').on('change', function() {
        $('.row-check').prop('checked', this.checked);
    });

    // ✅ Nếu bỏ 1 checkbox → bỏ chọn check-all
    $(document).on('change', '.row-check', function() {
        let total = $('.row-check').length;
        let checked = $('.row-check:checked').length;
        $('#check-all').prop('checked', total === checked);
    });

    // ✅ Hàm lưu
    function saveRows(type) {
        let selectedIndexes = [];
        if (type === 'all') {
            $('.row-check').each(function() { selectedIndexes.push($(this).val()); });
        } else {
            $('.row-check:checked').each(function() { selectedIndexes.push($(this).val()); });
        }

        if (selectedIndexes.length === 0) {
            alert('⚠️ Không có dòng nào được chọn để lưu!');
            return;
        }

        const excelData = JSON.parse($('#excel-data').val());
        const selectedData = selectedIndexes.map(i => excelData[i]);

        $.ajax({
            url: '<?= Url::to(['import-excel/save-import']) ?>',
            type: 'POST',
            data: JSON.stringify({ rows: selectedData }),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    alert('✅ ' + res.message);
                    location.reload();
                } else {
                    alert('❌ ' + res.message);
                }
            },
            error: function() {
                alert('❌ Lỗi kết nối máy chủ!');
            }
        });
    }

    $('#save-all, #save-all-bottom').on('click', function() { saveRows('all'); });
    $('#save-selected, #save-selected-bottom').on('click', function() { saveRows('selected'); });
});
</script>

<?php else: ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i> Không có dữ liệu để hiển thị.
</div>
<?php endif; ?>
