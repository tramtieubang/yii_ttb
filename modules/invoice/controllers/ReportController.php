<?php

namespace app\modules\invoice\controllers;

use Yii;
use yii\web\Controller;
use Mpdf\Mpdf;
use app\modules\invoice\models\Invoice;       // Model hóa đơn
use app\models\InvoiceDetail; // Model chi tiết hóa đơn
use app\modules\invoice\models\InvoiceForm;
use app\modules\products\models\ProductsForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use yii\helpers\Html;

class ReportController extends Controller
{
    public $layout = false; // không dùng layout web

    /**
     * Xuất PDF hóa đơn
     * @param int $id ID của hóa đơn
     */
    protected function findModel($id)
    {
        $model = \app\modules\invoice\models\InvoiceForm::findOne($id);
        if ($model !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('Hóa đơn không tồn tại.');
    }

    // KO SU DUNG
    public function actionPdfReport1($id)
    {
        // --- Lấy dữ liệu hóa đơn ---
        $invoice = InvoiceForm::findOne($id);
        if (!$invoice) {
            throw new \yii\web\NotFoundHttpException("Hóa đơn #$id không tồn tại");
        }

         // --- Lấy chi tiết hóa đơn ---
        $details = $invoice->invoiceDetails;
        /* $details = InvoiceDetail::find()
            ->where(['invoice_id' => $id])
            ->asArray()
            ->all();
        */

        // --- Lấy danh sách sản phẩm, đơn vị và giá ---
        $products = ProductsForm::find()->with(['latestProductPricesUnit.unit'])->all();
        $latestPrices = [];
        foreach ($products as $p) {
            if (!empty($p->latestProductPricesUnit)) {
                foreach ($p->latestProductPricesUnit as $price) {
                    $latestPrices[] = [
                        'id' => $price->id,
                        'text' => "{$p->name} - {$price->unit->name} - " . number_format($price->price, 2, ',', '.') . " ₫",
                        'price' => $price->price,
                        'product_id' => $p->id,
                        'unit_id' => $price->unit_id,
                    ];
                }
            }
        }    

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'title' => "Test modal 2",
            'content' => $this->renderAjax('modal-content', [
                'model' => $invoice,
                'details' => $details,
                'latestPrices' => $latestPrices,
            ]),
            'footer' => 
                Html::button('Đóng', ['class' => 'btn btn-secondary', 'data-bs-dismiss'=>"modal"]) .
                Html::button('Lưu lại', ['class' => 'btn btn-primary', 'type'=>"submit"]),
        ];

    }

    public function actionPdfReport($id)
    {
        // --- Lấy dữ liệu hóa đơn ---
        $invoice = InvoiceForm::findOne($id);
        if (!$invoice) {
            throw new \yii\web\NotFoundHttpException("Hóa đơn #$id không tồn tại");
        }

         // --- Lấy chi tiết hóa đơn ---
        $details = $invoice->invoiceDetails;
        /* $details = InvoiceDetail::find()
            ->where(['invoice_id' => $id])
            ->asArray()
            ->all();
        */

        // --- Lấy danh sách sản phẩm, đơn vị và giá ---
        $index = 1;
        $data = '';

        foreach ($details as $detail) {            
            $data .= '<tr>
                <td class="text-center">' . ($index++) . '</td>
                <td>' . $detail->productPriceUnit->product->name . '</td>
                <td>' . $detail->productPriceUnit->unit->name . '</td>
                <td class="text-end">' . $detail->quantity . '</td>
                <td class="text-end">' . number_format($detail->unit_price, 2, ',', '.') . '</td>
                <td class="text-end">' . number_format($detail->total, 2, ',', '.') . '</td>
                <td>' . $detail->notes . '</td>
            </tr>';
        }

        // --- Render template PDF ---
        $html = file_get_contents(Yii::getAlias('@app/modules/invoice/views/report/pdf_template.php'));
        $html = strtr($html, [
            '${so_hoa_don}' => $invoice->invoice_number,
            '${ten_khach_hang}' => $invoice->customer->name,
            '${dia_chi}' => $invoice->customer->address,
            '${dien_thoai}' => $invoice->customer->phone,
            '${ngay_lap}' => Yii::$app->formatter->asDate($invoice->issue_date,'php:d/m/Y'),
            '${ngay_den_han}' => Yii::$app->formatter->asDate($invoice->due_date,'php:d/m/Y'),
            '${phuong_thuc_thanh_toan}' => ucfirst($invoice->payment_method),
            '${year}' => ucfirst($invoice->status),
            '${trang_thai}' => ucfirst($invoice->status),
            '${data}' => $data,
            '${subtotal}' => number_format($invoice->subtotal, 2, ',', '.'),
            '${discount_total}' => number_format($invoice->discount_total, 2, ',', '.'),
            '${tax_total}' => number_format($invoice->tax_total, 2, ',', '.'),
            '${total_amount}' => number_format($invoice->total_amount, 2, ',', '.'),
            '${invoice_notes}' => $invoice->notes,
        ]);

        // --- Khởi tạo MPDF ---
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'DejaVu Sans', // hỗ trợ tiếng Việt
        ]);

        // --- Header và Footer ---
        //$mpdf->SetHTMLHeader('<div style="text-align:center; font-weight:bold; font-size:14pt;">HÓA ĐƠN BÁN HÀNG</div>');
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:10pt;">Ngày xuất: '.date('d-m-Y').' | Trang {PAGENO}</div>');



        $mpdf->WriteHTML($html);
       // $fileName = 'Bao-gia-cap-phoi-nhua' . preg_replace('/[^A-Za-z0-9\-]/', '', $customer->name) . '-' . $customer->phone . '.pdf';
        return Yii::$app->response->sendContentAsFile(
            $mpdf->Output("hoa_don_{$invoice->invoice_number}.pdf", 'D'),
            ['mimeType' => 'application/pdf']
        );

    }

    // XUẤT EXCEL KHÔNG THEO TEMPLATE CỐ ĐỊNH CỘT
   public function actionExcelReport($id)
    {
        $model = $this->findModel($id);
        $details = $model->invoiceDetails;

        // Tạo file Excel mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hóa đơn');

        // Đặt font mặc định cho toàn bộ Sheet
        $sheet->getParent()->getDefaultStyle()->getFont()
                            ->setName('Times New Roman')
                            ->setSize(13); // tuỳ chọn cỡ chữ mặc định

        // === PHẦN TIÊU ĐỀ HÓA ĐƠN ===
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'HÓA ĐƠN BÁN HÀNG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('0070C0');;
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A2', 'Số: ' . $model->invoice_number);

        // Thông tin hóa đơn        
        $sheet->mergeCells('A3:B3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('A3', 'Khách hàng:');

        $sheet->mergeCells('C3:D3');
        $sheet->getStyle('C3')->getFont()->setSize(13);
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C3', ($model->customer->name ?? ''));

        $sheet->mergeCells('A4:B4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('A4', 'Ngày lập:');

        $sheet->mergeCells('C4:D4');
        $sheet->getStyle('C4')->getFont()->setSize(13);
        $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C4', (Yii::$app->formatter->asDate($model->issue_date, 'php:d/m/Y')));

        $sheet->mergeCells('A5:B5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('A5', 'Hạn thanh toán:');

        $sheet->mergeCells('C5:D5');
        $sheet->getStyle('C5')->getFont()->setSize(13);
        $sheet->getStyle('C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C5', (Yii::$app->formatter->asDate($model->due_date, 'php:d/m/Y')));

        $sheet->mergeCells('E3:I3');
        $sheet->getStyle('E3')->getFont()->setSize(13);
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        //$sheet->setCellValue('E3', 'Phương thức thanh toán: ' . ($model->payment_method ?? ''));
         // Tạo đối tượng RichText
        $richText = new RichText();
        // Thêm phần "Trạng thái:" in đậm
        $boldPart = $richText->createTextRun('Phương thức thanh toán: ');
        $boldPart->getFont()->setBold(true)->setName('Times New Roman')->setSize(13);
        // Thêm phần nội dung trạng thái bình thường
        $statusText = $model->status ?? '';
        $richText->createText($statusText);
        // Gán vào ô E4
        $sheet->setCellValue('E3', $richText);

        $sheet->mergeCells('E4:I4');
        $sheet->getStyle('E4')->getFont()->setSize(13);
        $sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        // $sheet->setCellValue('E4', 'Trạng thái: ' . ($model->status ?? ''));
        // Tạo đối tượng RichText
        $richText = new RichText();
        // Thêm phần "Trạng thái:" in đậm
        $boldPart = $richText->createTextRun('Trạng thái: ');
        $boldPart->getFont()->setBold(true)->setName('Times New Roman')->setSize(13);
        // Thêm phần nội dung trạng thái bình thường
        $statusText = $model->status ?? '';
        $richText->createText($statusText);
        // Gán vào ô E4
        $sheet->setCellValue('E4', $richText);

        // === PHẦN BẢNG CHI TIẾT ===
        $headerRow = 7;
        // Gộp ô trước khi đặt giá trị
        $sheet->mergeCells("B{$headerRow}:C{$headerRow}");
        $sheet->mergeCells("F{$headerRow}:G{$headerRow}");

        // Danh sách tiêu đề (chỉ còn 5 thay vì 7, vì B–C và F–G đã gộp)
        $headers = ['STT', 'Sản phẩm', 'ĐVT', 'Số lượng', 'Đơn giá', 'Thành tiền', 'Ghi chú'];
        $columns = ['A', 'B', 'D', 'E', 'F', 'H','I']; // chú ý điều chỉnh cột cho phù hợp

        // Nếu bạn vẫn muốn giữ mapping cũ (A–G), làm cách dưới đây:
        $sheet->setCellValue("A{$headerRow}", 'STT');
        $sheet->setCellValue("B{$headerRow}", 'Sản phẩm');
        $sheet->setCellValue("D{$headerRow}", 'ĐVT');
        $sheet->setCellValue("E{$headerRow}", 'Số lượng');        
        $sheet->setCellValue("F{$headerRow}", 'Đơn giá');       
        $sheet->setCellValue("H{$headerRow}", 'Thành tiền');
        $sheet->setCellValue("I{$headerRow}", 'Ghi chú');

        // === Định dạng style cho toàn hàng tiêu đề ===
       // === Định dạng style cho toàn hàng tiêu đề ===
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFont()
            ->setBold(true)
            ->setName('Times New Roman')
            ->getColor()->setRGB('000000'); // màu đen

        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDDDDD'); // nền xám sáng

        // Tự động giãn cột
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Ghi đè kích thước cụ thể cho các cột cần cố định
        $sheet->getColumnDimension('c')->setAutoSize(false)->setWidth(8);
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(8);
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(5);
        $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(5);
        $sheet->getColumnDimension('H')->setAutoSize(false)->setWidth(11);

        /* 
        $headers = ['STT', 'Sản phẩm', 'ĐVT', 'Số lượng', 'Đơn giá', 'Thành tiền', 'Ghi chú'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $index => $header) {
            
            $col = $columns[$index];
            $sheet->setCellValue("{$col}{$headerRow}", $header);
            $sheet->getStyle("{$col}{$headerRow}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle("{$col}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$col}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        } */

        // Ghi dữ liệu chi tiết
        $row = $headerRow + 1;
        foreach ($details as $i => $d) {
            // Căn giữa STT và số lượng
            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("A{$row}", $i + 1);

            $sheet->mergeCells("B{$row}:C{$row}");
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue("B{$row}", $d->productPriceUnit->product->name ?? '');

            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("D{$row}", $d->productPriceUnit->unit->name ?? '');

            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("E{$row}", $d->quantity);
            
            // Canh phải đơn giá và thành tiền
            $sheet->mergeCells("F{$row}:G{$row}");
            $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue("F{$row}", number_format($d->unit_price, 2, ',', '.'));

            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue("H{$row}", number_format($d->total, 2, ',', '.'));
            $sheet->setCellValue("I{$row}", $d->notes);

            $row++;
        }

        // Kẻ viền cho toàn bộ bảng
        $sheet->getStyle("A{$headerRow}:I" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // === PHẦN TỔNG CỘNG ===
        $row += 1;
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("E{$row}")->getFont()->setSize(13);
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("E{$row}", 'Tạm tính:');

        // Kẻ viền quanh vùng gộp (E:G)
        $sheet->getStyle("E{$row}:G{$row}")
            ->getBorders()->getTop()    //getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000'); // màu viền đen

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->getStyle("H{$row}")->getFont()->setSize(13);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("H{$row}", number_format($model->subtotal, 2, ',', '.'));

        // Kẻ viền quanh vùng gộp (E:G)
        $sheet->getStyle("H{$row}:I{$row}")
            ->getBorders()->getTop()    //getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000'); // màu viền đen

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setSize(13);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("A{$row}", 'Ghi chú:');

        $row++;
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("E{$row}")->getFont()->setSize(13);
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("E{$row}", 'Giảm giá:');

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->getStyle("H{$row}")->getFont()->setSize(13);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("H{$row}", number_format($model->discount_total, 2, ',', '.'));

        $row++;
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("E{$row}")->getFont()->setSize(13);
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("E{$row}", 'Thuế:');

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->getStyle("H{$row}")->getFont()->setSize(13);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("H{$row}", number_format($model->tax_total, 2, ',', '.'));

        $row++;
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("E{$row}")->getFont()->setSize(14)->setBold(true)->getColor()->setRGB('0070C0');
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue("E{$row}", 'Tổng cộng:');

        // Kẻ viền quanh vùng gộp (E:G)
        $sheet->getStyle("E{$row}:G{$row}")
            ->getBorders()->getTop()    //getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000'); // màu viền đen

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->getStyle("H{$row}")->getFont()->setSize(14)->setBold(true)->getColor()->setRGB('0070C0');
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);  // Canh phải phần tổng
        $sheet->setCellValue("H{$row}", number_format($model->total_amount, 2, ',', '.'));

         // Kẻ viền quanh vùng gộp (E:G)
        $sheet->getStyle("H{$row}:I{$row}")
            ->getBorders()->getTop()    //getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000'); // màu viền đen

        $sheet->getStyle("E{$row}:I{$row}")->getFont()->setBold(true)->setSize(14);

        // === PHẦN KÝ TÊN ===
        $row += 3;
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("A{$row}", 'Người lập hóa đơn');        

        $row1 = $row + 1;
        $sheet->mergeCells("A{$row1}:C{$row1}");
        $sheet->getStyle("A{$row1}")->getFont()->setSize(12);
        $sheet->getStyle("A{$row1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("A{$row1}", '(Ký, ghi rõ họ tên)');  

        $sheet->mergeCells("E{$row}:I{$row}");
        $sheet->getStyle("E{$row}")->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("E{$row}", 'Khách hàng');        

        $sheet->mergeCells("E{$row1}:I{$row1}");
        $sheet->getStyle("E{$row1}")->getFont()->setSize(12);
        $sheet->getStyle("E{$row1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("E{$row1}", '(Ký, ghi rõ họ tên)');  

        // === XUẤT FILE ===
        $filename = 'HoaDon_' . $model->invoice_number . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // XUẤT EXCEL THEO TEMPLATE CỐ ĐỊNH DÒNG CỘT
    public function actionExcelReportTemplate($id)
    {
        $model = $this->findModel($id);
        $details = $model->invoiceDetails;

        // Đường dẫn đến file template
        $templatePath = Yii::getAlias('@app/modules/invoice/views/report/excel_report_template.xlsx');

        // Load template
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Gán dữ liệu thông tin hóa đơn
        //$sheet->setCellValue('B2', 'HÓA ĐƠN BÁN HÀNG');
        $sheet->setCellValue('A2', 'Số: ' . $model->invoice_number);
        $sheet->setCellValue('C3', $model->customer->name);
        $sheet->setCellValue('C4', Yii::$app->formatter->asDate($model->issue_date, 'php:d/m/Y'));
        $sheet->setCellValue('C5', Yii::$app->formatter->asDate($model->due_date, 'php:d/m/Y'));
        $sheet->setCellValue('H3', $model->payment_method);
        $sheet->setCellValue('H4', $model->status);

        // Vòng lặp ghi chi tiết hóa đơn bắt đầu từ hàng 10
        $row = 8;
        foreach ($details as $i => $d) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $d->productPriceUnit->product->name ?? '');
            $sheet->setCellValue("B{$row}", $d->productPriceUnit->unit->name ?? '');
            $sheet->setCellValue("C{$row}", $d->quantity);
            $sheet->setCellValue("F{$row}", number_format($d->unit_price, 2, ',', '.'));
            $sheet->setCellValue("H{$row}", number_format($d->total, 2, ',', '.'));
            $sheet->setCellValue("I{$row}", $d->notes);
            $row++;
        }

        // Tổng cộng
        $row++;
        $sheet->setCellValue("H{$row}", number_format($model->subtotal, 2, ',', '.'));
        $row++;
        $sheet->setCellValue("H{$row}", number_format($model->discount_total, 2, ',', '.'));
        $row++;
        $sheet->setCellValue("H{$row}", number_format($model->tax_total, 2, ',', '.'));
        $row++;
        $sheet->setCellValue("H{$row}", number_format($model->total_amount, 2, ',', '.'));

        $row = $row + 10;
        $sheet->setCellValue("H{$row}", $model->customer->name);

        // Xuất file ra trình duyệt
        $filename = 'HoaDon_' . $model->invoice_number . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


}
