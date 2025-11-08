<?php

namespace app\modules\invoice\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;

use app\models\Invoice;
use app\models\InvoiceDetail;
use app\modules\invoice\models\InvoiceForm;
use app\modules\invoice\models\InvoiceSearch;
use app\modules\products\models\ProductsForm;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /** ===================== DANH SÁCH ===================== */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();

        if (Yii::$app->request->post('search')) {
            $dataProvider = $searchModel->search(Yii::$app->request->post(), Yii::$app->request->post('search'));
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** ===================== XEM CHI TIẾT ===================== */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $details = $model->invoiceDetails;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Xem hóa đơn #{$model->invoice_number}",
                'content' => $this->renderAjax('view', compact('model', 'details')),
                'footer' =>
                    Html::button('Đóng', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"]) .
                    Html::a('Sửa', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        }

        return $this->render('view', compact('model', 'details'));
    }

    /** ===================== THÊM MỚI ===================== */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new InvoiceForm();

        // Lấy danh sách sản phẩm + đơn vị + giá
        $latestPrices = ProductsForm::find()
            ->with(['latestProductPricesUnit.unit'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Hiển thị form
            if ($request->isGet) {
                return [
                    'title' => "Thêm mới hóa đơn",
                    'content' => $this->renderAjax('create', compact('model', 'latestPrices')),
                    'footer' =>
                        Html::button('Đóng', ['class' => 'btn btn-outline-secondary', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Lưu lại', ['class' => 'btn btn-primary', 'type' => "submit"]),
                ];
            }

            // Lưu dữ liệu
            if ($model->load($request->post()) && $model->validate()) {

                /* $data = [
                    'invoice_number' => $model->invoice_number,
                    'customer_id'    => $model->customer_id,
                    'status'         => $model->status,
                    'payment_method' => $model->payment_method,
                    'issue_date'     => $model->issue_date ?: date('Y-m-d'),
                    'due_date'       => $model->due_date ?: date('Y-m-d'),
                    'notes'          => $model->notes,
                    'subtotal'       => $model->subtotal ?: 0,
                    'discount'       => $model->discount_total ?: 0,
                    'tax'            => $model->tax_total ?: 0,
                    'total'          => $model->total_amount ?: 0
                ];  
                dd($data); */

                $result = $this->saveInvoice($model);

                return [
                    'title' => "Thêm mới hóa đơn",
                    'content' => '<span class="text-' . ($result['status'] ? 'success' : 'danger') . '">' . Html::encode($result['message']) . '</span>',
                    'tcontent'=>'Thêm mới hóa đơn thành công!',
                    'forceClose' => $result['status'] === 1,
                    'forceReload' => $result['status'] === 1 ? '#crud-datatable-pjax' : null,
                    'footer' =>
                        Html::button('Đóng', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::a('Thêm mới', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote']),
                ];
            }

            // Nếu validate lỗi
            return [
                'title' => "Thêm mới hóa đơn",
                'content' => $this->renderAjax('create', compact('model', 'latestPrices')),
                'tcontent' => Html::errorSummary($model),
                'footer' =>
                    Html::button('Đóng', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"]) .
                    Html::button('Lưu lại', ['class' => 'btn btn-primary', 'type' => "submit"]),
            ];
        }

        // Non-AJAX
        if ($model->load($request->post()) && $model->validate()) {
            $result = $this->saveInvoice($model);
            if ($result['status'] === 1) {
                Yii::$app->session->setFlash('success', $result['message']);
                return $this->redirect(['view', 'id' => $result['id']]);
            }
            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->render('create', compact('model', 'latestPrices'));
    }

    /** ===================== LƯU HÓA ĐƠN ===================== */
    protected function saveInvoice($model)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // 🔸 Kiểm tra trùng số hóa đơn
            $exists = Invoice::find()
                ->where(['invoice_number' => $model->invoice_number])
                ->andFilterWhere(['<>', 'id', $model->id]) // bỏ qua chính nó khi update
                ->exists();

            if ($exists) {
                return [
                    'status' => 0,
                    'message' => '⚠️ Số hóa đơn này đã tồn tại. Vui lòng nhập số khác.',
                ];
            }

            // --- Tạo hoặc lấy hóa đơn ---
            $invoice = $model->id ? Invoice::findOne($model->id) : new Invoice();

            $invoice->invoice_number = $model->invoice_number;
            $invoice->customer_id = $model->customer_id;
            $invoice->status = $model->status;
            $invoice->payment_method = $model->payment_method;
            $invoice->issue_date = $model->issue_date ?: date('Y-m-d');
            $invoice->due_date = $model->due_date ?: date('Y-m-d');
            $invoice->notes = $model->notes;
            $invoice->subtotal = $model->subtotal ?: 0;
            $invoice->discount_total = $model->discount_total ?: 0;
            $invoice->tax_total = $model->tax_total ?: 0;
            $invoice->total_amount = $model->total_amount ?: 0;

            if (!$invoice->save()) {
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'message' => '❌ Không thể lưu hóa đơn. Vui lòng kiểm tra lại dữ liệu.',
                ];
            }

            // --- Xóa chi tiết cũ nếu cập nhật ---
            InvoiceDetail::deleteAll(['invoice_id' => $invoice->id]);

            // --- Lưu chi tiết ---
            $details = Yii::$app->request->post('InvoiceDetail', []);
            $countSaved = 0;

            foreach ($details as $d) {
                if (empty($d['product_price_unit_id'])) continue;

                $detail = new InvoiceDetail();
                $detail->invoice_id = $invoice->id;
                $detail->product_price_unit_id = $d['product_price_unit_id'];
                $detail->quantity = (float)($d['quantity'] ?? 0);
                $detail->unit_price = isset($d['unit_price']) 
                    ? floatval(str_replace(',', '.', str_replace('.', '', $d['unit_price'])))
                    : 0;
                $detail->total = isset($d['total'])
                    ? floatval(str_replace(',', '.', str_replace('.', '', $d['total'])))
                    : 0;
                $detail->notes = $d['notes'] ?? '';

                if (!$detail->save()) {
                    throw new \Exception('Chi tiết hóa đơn lỗi: ' . json_encode($detail->getErrors(), JSON_UNESCAPED_UNICODE));
                }
                $countSaved++;
            }

            if ($countSaved === 0) {
                throw new \Exception('Hóa đơn chưa có chi tiết sản phẩm nào.');
            }

            $transaction->commit();

            return [
                'status' => 1,
                'id' => $invoice->id,
                'message' => '✅ Lưu hóa đơn và chi tiết thành công!',
            ];

        } catch (\Throwable $e) {
            $transaction->rollBack();

            return [
                'status' => 0,
                'message' => '❌ Lưu không thành công. Lỗi: ' . $e->getMessage(),
            ];
        }
    }


    /** ===================== CẬP NHẬT ===================== */
   public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $details = $model->invoiceDetails;

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

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            // GET: trả view
            if ($request->isGet) {
                return [
                    'title' => "Cập nhật hóa đơn #{$model->invoice_number}",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'details' => $details,
                        'latestPrices' => $latestPrices,
                    ]),
                    'footer' =>
                        Html::button('Đóng', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Lưu lại', ['class' => 'btn btn-primary', 'type' => "submit"]),
                ];
            }

            // POST: lưu dữ liệu
            if ($model->load($request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Lấy mảng detail từ POST — LƯU Ý: tên key phải khớp với form
                    $detailsData = $request->post('InvoiceDetail', []);
                    $keepIds = [];

                    foreach ($detailsData as $row) {
                        // IMPORTANT: form dùng product_price_unit_id (không phải product_id)
                        if (empty($row['product_price_unit_id'])) {
                            // nếu muốn loại bỏ những dòng rỗng, tiếp tục
                            continue;
                        }

                        // Nếu có id thì load, ngược lại tạo mới
                        $detail = !empty($row['id']) ? InvoiceDetail::findOne($row['id']) : new InvoiceDetail();

                        $detail->invoice_id = $model->id;
                        $detail->product_price_unit_id = $row['product_price_unit_id'];

                        // parse số: loại dấu chấm hàng nghìn, chuyển dấu phẩy thành dấu chấm cho float
                        $parseNumber = function($v) {
                            if ($v === null || $v === '') return 0;
                            // loại spaces, non-digits
                            $s = (string)$v;
                            $s = str_replace(' ', '', $s);
                            $s = str_replace(['.', ','], ['', '.'], $s); // '1.234,56' -> '1234.56'
                            return (float)$s;
                        };

                        $detail->quantity = $parseNumber($row['quantity'] ?? 0);
                        $detail->unit_price = $parseNumber($row['unit_price'] ?? 0);
                        $detail->total = $parseNumber($row['total'] ?? ($detail->quantity * $detail->unit_price));
                        $detail->notes = $row['notes'] ?? '';

                        if (!$detail->save(false)) {
                            throw new \Exception('Không lưu được chi tiết: ' . json_encode($detail->getErrors(), JSON_UNESCAPED_UNICODE));
                        }

                        $keepIds[] = $detail->id;
                    }

                    // Xóa những detail cũ không có trong $keepIds
                    if (empty($keepIds)) {
                        // nếu không còn dòng nào, xóa toàn bộ detail của hóa đơn
                        InvoiceDetail::deleteAll(['invoice_id' => $model->id]);
                    } else {
                        InvoiceDetail::deleteAll([
                            'and',
                            ['invoice_id' => $model->id],
                            ['not in', 'id', $keepIds]
                        ]);
                    }

                    // Cập nhật subtotal/total nếu bạn muốn từ server (không bắt buộc)
                    // $model->subtotal = ...; $model->total_amount = ...;

                    // Lưu hóa đơn chính (bỏ qua validate nếu bạn đã validate trước)
                    if (!$model->save(false)) {
                        throw new \Exception('Không lưu được hóa đơn chính: ' . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE));
                    }

                    $transaction->commit();

                    $model = $this->findModel($id);
                    $details = $model->invoiceDetails;

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

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Cập nhật hóa đơn #{$model->invoice_number}",
                        'content' => $this->renderAjax('view', [
                            'model' => $model,
                            'latestPrices' => $latestPrices,
                            'details' => $model->invoiceDetails,
                        ]),
                        'tcontent' => 'Cập nhật thành công!',
                        'footer' =>
                            Html::button('Đóng', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => "modal"]) .
                            Html::a('Sửa lại', ['update', 'id' => $id], [
                                'class' => 'btn btn-primary',
                                'role' => 'modal-remote',
                            ]),
                    ];
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage(), __METHOD__);
                    return [
                        'title' => "Cập nhật hóa đơn #{$model->invoice_number}",
                        'content' => '<div class="text-danger">Lỗi khi lưu: ' . Html::encode($e->getMessage()) . '</div>',
                        'footer' => Html::button('Đóng', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => "modal"]),
                    ];
                }
            }

            // Nếu load POST mà validate thất bại (hoặc không load)
            return [
                'title' => "Cập nhật hóa đơn #{$model->invoice_number}",
                'content' => $this->renderAjax('update', [
                    'model' => $model,
                    'details' => $details,
                    'latestPrices' => $latestPrices,
                ]),
                'footer' =>
                    Html::button('Đóng', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => "modal"]) .
                    Html::button('Lưu lại', ['class' => 'btn btn-primary', 'type' => "submit"]),
            ];
        }

        // Non-AJAX render
        return $this->render('update', [
            'model' => $model,
            'details' => $details,
            'latestPrices' => $latestPrices,
        ]);
    }

    /** ===================== XÓA ===================== */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        }

        return $this->redirect(['index']);
    }

    /** ===================== XÓA NHIỀU ===================== */
    public function actionBulkdelete()
    {
        $pks = explode(',', Yii::$app->request->post('pks', ''));
        $delOk = true;
        $failed = [];

        foreach ($pks as $pk) {
            try {
                $model = $this->findModel($pk);
                $model->delete();
            } catch (\Exception $e) {
                $delOk = false;
                $failed[] = $pk;
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'tcontent' => $delOk ? 'Xóa thành công!' : ('Không thể xóa ID: ' . implode(', ', $failed)),
            ];
        }

        return $this->redirect(['index']);
    }

    public function actionCustomer()
    {
        return $this->render("fasdf");
    }

    /** ===================== PDF ===================== */
   /*  public function actionExportPdf($id)
    {
        $this->layout = false;

        $model = InvoiceForm::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Không tìm thấy hóa đơn.");
        }

        $html = $this->renderPartial('pdf', compact('model'));

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->SetTitle("Hóa đơn #{$model->id}");
        $mpdf->WriteHTML($html);
        return $mpdf->Output("HoaDon_{$model->id}.pdf", \Mpdf\Output\Destination::INLINE);
    } */

    /** ===================== FIND MODEL ===================== */
    protected function findModel($id)
    {
        if (($model = InvoiceForm::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Không tìm thấy hóa đơn.');
    }

   /*  public function actionExportPdf()
{
    $models = Report::find()->all();
    $content = $this->renderPartial('view', ['models' => $models]);

    $pdf = new \Mpdf\Mpdf(['format'=>'A4']);
    $pdf->WriteHTML($content);
    return $pdf->Output('bao_cao.pdf', 'I');
}

public function actionExportExcel()
{
    $models = Report::find()->all();
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1','STT');
    $sheet->setCellValue('B1','Tên mục');
    $sheet->setCellValue('C1','Số lượng');
    $sheet->setCellValue('D1','Ghi chú');

    $row = 2;
    foreach($models as $i => $m){
        $sheet->setCellValue('A'.$row, $i+1);
        $sheet->setCellValue('B'.$row, $m->name);
        $sheet->setCellValue('C'.$row, $m->quantity);
        $sheet->setCellValue('D'.$row, $m->note);
        $row++;
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="bao_cao.xlsx"');
    $writer->save('php://output');
    exit;
} */


}
