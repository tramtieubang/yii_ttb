<?php

namespace app\modules\categories\controllers;

use app\modules\categories\models\CategoriesForm;
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

                /* // Chuyển sang mảng key rõ ràng
                foreach ($sheetData as $row) {
                    $data[] = [
                        'id'    => $row['A'] ?? '',
                        'name'    => $row['B'] ?? '',
                        'email'   => $row['C'] ?? '',
                        'phone'   => $row['D'] ?? '',
                        'address' => $row['E'] ?? '',
                        'note'    => $row['F'] ?? '',
                    ];
                } */

                $data = $sheetData;
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
    public function actionSaveImport()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $body = Yii::$app->request->getRawBody();
        $json = json_decode($body, true);
        $rows = $json['rows'] ?? [];

        if (empty($rows)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để lưu!'];
        }

        $count = 0;
        foreach ($rows as $r) {
            $model = new CategoriesForm();
            //$model->id = $r['id'] ?? $r['A'] ?? null;
            $model->name = $r['name'] ?? $r['B'] ?? null;
            $model->description = $r['description'] ?? $r['C'] ?? null;

            if ($model->save(false)) $count++;
        }

        return ['success' => true, 'message' => "Đã lưu {$count} dòng thành công!"];
    }


}
