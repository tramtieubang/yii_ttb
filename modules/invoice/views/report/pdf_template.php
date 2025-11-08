<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hóa đơn</title>

<style>
    body {
        font-size: 13px;
        margin: 0;
        padding: 0;
        background: #fff;
        font-family: 'Times New Roman', Times, serif;
        color: #333;
    }

    .invoice-container {
        background: #fff;
        border-radius: 8px;
        padding: 5px 5px 5px 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.08);
        margin: 0 auto;
        width: 210mm;
        min-height: 297mm;
        box-sizing: border-box;
    }

/* ===== HEADER ===== */
.invoice-header {
    text-align: center;
    margin-bottom: 18px;
    position: relative;
    padding-bottom: 10px;
}
.invoice-header::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 35%;
    width: 30%;
    height: 2px;
    background: #007bff;
}
.invoice-header h2 {
    font-size: 22px;
    font-weight: bold;
    color: #007bff;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.invoice-header p {
    margin: 5px 0 0 0;
    font-size: 13px;
}

/* ===== THÔNG TIN ===== */
.invoice-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}
.invoice-info p {
    margin: 3px 0;
    font-size: 13px;
}

/* ===== BẢNG CHI TIẾT ===== */
.table-invoice {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    border: 1px solid #444;
}

.table-invoice th,
.table-invoice td {
    border: 1px solid #444;
    padding: 6px 8px;
    font-size: 13px;
    vertical-align: middle;
}

.table-invoice th {
    background: #f2f2f2;
    text-transform: uppercase;
    text-align: center;
    font-weight: bold;
}

.table-invoice td.text-end {
    text-align: right;
}

/* ===== TỔNG KẾT ===== */
/* Tổng cộng */

.invoice-summary {
    border-top: 2px solid #97989aff;
    padding-top: 10px;
    margin-top: 20px;
    font-size: 15px;
}

.invoice-summary table {
    width: 100%;
    border-collapse: collapse;
}
.invoice-summary td {
    padding: 4px 6px;
}
.invoice-summary tr:last-child td {
    font-weight: bold;
    color: #007bff;
    font-size: 15px;
}

/* ===== GHI CHÚ ===== */
.notes {
    margin-top: 10px;
    font-style: italic;
}

/* ===== CHỮ KÝ ===== */
.invoice-sign {
    display: flex;
    justify-content: space-between;
    text-align: center;  
}

.invoice-sign p {
    font-weight: bold;
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
.row.text-center {
    border: #007bff solid 1px;
    margin-top: 50px;
    vertical-align: top;        /* 👈 căn nội dung lên trên */
    text-align: center !important;
    width: 100% !important;
    display: inline-block !important; 
}

/* ===== RESPONSIVE & PRINT ===== */
@media print {
    body {
        background: #fff;
    }
    .invoice-container {
        box-shadow: none;
        padding: 3mm 10mm 5mm 5mm;
        margin: 0;
        width: 100%;
    }
}
</style>
</head>
<body>

<div id="invoice-content" class="invoice-container">

    <!-- Header -->
    <div class="invoice-header">
        <h2>HÓA ĐƠN BÁN HÀNG</h2>
        <p>Số: ${so_hoa_don}</p>
    </div>

    <!-- Thông tin -->
    <div class="row mb-4 invoice-info">
        <div class="col-md-6">
            <p><strong>Khách hàng:</strong> ${ten_khach_hang}</p>
            <p><strong>Ngày lập:</strong> ${ngay_lap}</p>
            <p><strong>Ngày đến hạn:</strong> ${ngay_den_han}</p>
        </div>
         <div class="col-md-6">
            <p><strong>Phương thức thanh toán:</strong> ${phuong_thuc_thanh_toan}</p>
            <p><strong>Trạng thái:</strong> ${trang_thai}</p>
        </div>
    </div>

    <!-- Bảng chi tiết -->
    <table class="table-invoice">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Đơn vị tính</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            ${data}
        </tbody>
    </table>

    <!-- Tổng kết -->
    <div class="row mt-4">
        <div style="width: 55%; float: left;">
            <div class="notes">
                <strong>Ghi chú:</strong> ${invoice_notes}
            </div>
        </div>
        <div style="width: 40%; float: right;">
            <div class="invoice-summary">
                <table>
                    <tr>
                        <td>Tổng trước thuế:</td>
                        <td style="text-align:right;">${subtotal}</td>
                    </tr>
                    <tr>
                        <td>Giảm giá:</td>
                        <td style="text-align:right;">${discount_total}</td>
                    </tr>
                    <tr>
                        <td>Thuế:</td>
                        <td style="text-align:right;">${tax_total}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top:1px solid #aaa;"></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; font-size:15px; color: #0d6efd;">Tổng cộng:</td>
                        <td style="text-align:right; font-weight:bold; font-size:15px; color: #0d6efd;">
                            ${total_amount}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Chữ ký -->
     <div class="row text-center mt-5" style="border: solid #007bff 1px;">    
        <div class="col-md-6 invoice-sign">
            <strong>Người lập hóa đơn</strong><br>
            <small>(Ký, ghi rõ họ tên)</small>
        </div>
        <div class="col-md-6 invoice-sign">
            <strong>Khách hàng</strong><br>
            <small>(Ký, ghi rõ họ tên)</small>
        </div>
    </div>
</div>

</body>
</html>
