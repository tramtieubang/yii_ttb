<?php

namespace app\modules\products\controllers;

use app\modules\categories\models\CategoriesForm;
use app\modules\products\models\ProductsForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\Html;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExcelController extends Controller
{
    /**
     * Hiển thị form upload Excel (mở trong modal)
     */
    public function actionView()
    {
        $data = [];

        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'   => 'Import danh mục sản phẩm từ Excel',
                'content' => $this->renderAjax('../import/excel', [
                    'data' => $data,
                ]),
                'footer'  =>
                    Html::button('Đóng', [
                        'class' => 'btn btn-secondary',
                        'data-bs-dismiss' => 'modal',
                    ])
            ];
        }

        return $this->render('../import/excel', [
            'data' => $data,
        ]);
    }

    /**
     * Nhận file Excel qua AJAX và hiển thị bảng xem trước
     */
    public function actionImport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [];

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('excelFile');
            if ($file) {
                // ✅ Lưu file tạm
                $uploadPath = Yii::getAlias('@runtime/uploads/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0775, true);
                }

                $filePath = $uploadPath . $file->baseName . '.' . $file->extension;
                $file->saveAs($filePath);

                // ✅ Đọc dữ liệu Excel
                $spreadsheet = IOFactory::load($filePath);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                if (count($sheetData) > 1) {
                    array_shift($sheetData); // bỏ dòng tiêu đề
                }

                // Chuyển sang mảng key rõ ràng
                foreach ($sheetData as $row) {
                    $categoryId = $row['A'] ?? null;

                    // Lấy model danh mục
                    $category = CategoriesForm::findOne($categoryId);
                    $categoryName = $category ? $category->name : ''; // Nếu không tìm thấy, để rỗng

                    $data[] = [
                        'categories'    => $categoryName,
                        'category_id'    => $categoryId,
                        'name'    => $row['B'] ?? '',
                        'price'   => $row['C'] ?? '',
                        'datetime'   => $row['D'] ?? '',                       
                    ];
                } 
            }
        }

        return [
            'title'   => 'Xem trước dữ liệu từ Excel',
            'content' => $this->renderAjax('../import/_preview', [
                'data' => $data,
            ]),
            'footer'  =>
                Html::button('Đóng', [
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal',
                ]) .
                Html::button('Lưu vào hệ thống', [
                    'class' => 'btn btn-success',
                    'id' => 'btn-save-import',
                    'data-url' => \yii\helpers\Url::to(['save-import'])
                ])
        ];
    }

    /**
     * Lưu dữ liệu đã xem trước vào DB
     */
   // Lưu dữ liệu
    public function actionSaveImport()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $body = Yii::$app->request->getRawBody();
            $json = json_decode($body, true);
            $rows = $json['rows'] ?? [];

            if (empty($rows)) {
                return ['success' => false, 'message' => 'Không có dữ liệu để lưu!'];
            }

            $count = 0;
            foreach ($rows as $r) {
                // Kiểm tra category_id tồn tại
                $category = CategoriesForm::findOne($r['category_id']);
                if ($category) {
                    $model = new ProductsForm();
                    $model->category_id = $r['category_id'];
                    $model->name        = $r['name'] ?? null;
                    $model->price       = $r['price'] ?? null;
                    $model->datetime    = $r['datetime'] ?? null;

                    if (!$model->save(false)) {
                        return [
                            'success' => false, 
                            'message' => 'Lỗi lưu dữ liệu: ' . json_encode($model->errors)
                        ];
                    }
                    $count++;
                }
            }

            return ['success' => true, 'message' => "Đã lưu {$count} dòng thành công!"];

        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }



}
