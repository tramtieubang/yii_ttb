<?php

namespace app\modules\products\controllers;

use app\common\helpers\DateHelper;
use app\common\helpers\NumberHelper;
use app\models\ProductPricesUnit;
use app\modules\product_prices_unit\models\ProductPricesUnitForm;
use app\modules\products\models\ProductsForm;
use webvimark\components\BaseController;
use app\modules\user\models\User;
use app\modules\user\UserModule;
use yii\web\NotFoundHttpException;
use Yii;
use yii\web\Response;
use yii\helpers\Html;

class ProductPriceController extends BaseController
{
    public function actionCreate($productid)
    {
        $request = Yii::$app->request;
        $model = new ProductPricesUnitForm(); 

        // Hoặc Cách 2: lấy thủ công từ request
        //$id = Yii::$app->request->get('id');
         

        //$model->datetime = DateHelper::formatVN($model->datetime);  
       
        return $this->asJson([
            'title'=> "Thêm mới",
            'content' => $this->renderAjax('create', [
                'model' => $model,              // Truyền sản phẩm    
                'productid' => $productid,           
            ]),
            'footer' =>
                Html::button('Đóng lại', [
                    'class'=>'btn btn-default pull-left',
                    'data-bs-dismiss'=>"modal"
                ]) .
                Html::button('Lưu lại', [
                    'class'=>'btn btn-primary',
                    //'type'=>"submit",
                    'type' => 'button', // đổi từ submit → button để tránh reload form
                    'id' => 'btn-save-price'
                ]),
        ]);
    }

    public function actionSave1()
    {
        $request = Yii::$app->request;
        $model = new ProductPricesUnitForm();  

        if($model->load($request->post())){
            $model->price = NumberHelper::parseCurrency($model->price); 
            //var_dump($model->datetime);
            $model->datetime = DateHelper::toMySQL($model->datetime); 
            //var_dump($model->datetime);
            if($model->save()){
                // return $this->asJson([
                //     'forceReload'=>'#crud-datatable-pjax',
                //     'title'=> "Thêm mới",
                //     'content'=>'<span class="text-success">Thêm mới thành công</span>',
                //     'tcontent'=>'Thêm mới thành công!',
                //     'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                //             Html::a('Tiếp tục thêm',['/products/product-price/create','productid'=>$model->product_id],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                // ]);    
                 // giữ lại modal
                return $this->asJson([
                    'title'=> "Thêm mới",
                    'forceReload'=>'#crud-datatable-pjax', // Load lai GridView
                    'success' => true,
                    'content' => $this->renderAjax('create', [
                        'model' => $model,              // Truyền sản phẩm    
                        'productid' => $model->product_id,           
                    ]),
                    'tcontent'=>'Thêm mới thành công!',
                    'footer' =>
                        Html::button('Đóng lại', [
                            'class'=>'btn btn-default pull-left',
                            'data-bs-dismiss'=>"modal"
                        ]) .
                        Html::button('Lưu lại', [
                            'class'=>'btn btn-primary',
                            'type'=>"submit",
                            //'type' => 'button', // đổi từ submit → button để tránh reload form
                            'id' => 'btn-save-price'
                        ]),
                ]);
            }else {
                return $this->asJson([
                    'title'=> "Thêm mới",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,              // Truyền sản phẩm    
                        'productid' => $model->id,           
                    ]),
                    'tcontent'=>Html::errorSummary($model),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
        
                ]);   
            }  
        }else{           
             return $this->asJson([
                'title'=> "Thêm mới",
                'content' => $this->renderAjax('create', [
                    'model' => $model,              // Truyền sản phẩm    
                    'productid' => $model->id,           
                ]),
                'tcontent'=>Html::errorSummary($model),
                'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                            Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
    
            ]);         
        }       
    }

    // Luu voi ajax script
    public function actionSave()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $model = new ProductPricesUnitForm();  

        // Chỉ xử lý khi là POST
        if ($request->isPost && $model->load($request->post())) {

            // Chuẩn hóa dữ liệu
            $model->price = NumberHelper::parseCurrency($model->price); 
            $model->datetime = DateHelper::toMySQL($model->datetime); 

            if ($model->save()) {
                return [
                    'success' => true,
                    'resetForm' => true,
                    'forceReload' => '#crud-datatable-pjax',
                    'tcontent' => 'Thêm mới thành công!',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Lưu không thành công!',
                    'errors' => $model->getErrors(),
                ];
            }
        }

        // Nếu không phải POST hoặc load dữ liệu thất bại → render lại form
        return [
            'success' => false,
            'content' => $this->renderAjax('create', [
                'model' => $model,
                'productid' => $request->get('productid'),
            ]),
            'footer' =>
                Html::button('Đóng lại', [
                    'class' => 'btn btn-default pull-left',
                    'data-bs-dismiss' => "modal",
                ]) .
                Html::button('Lưu lại', [
                    'class' => 'btn btn-primary',
                    'type' => 'button',
                    'id' => 'btn-save-price',
                ]),
        ];
    }

    public function actionEdit($id)
    {
        // Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = ProductsForm::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Không tìm thấy sản phẩm ID: $id");
        }
        $latestPrices = $model->latestProductPricesUnit;

        return $this->asJson([
            'title' => "Cập nhật giá",
            'content' => $this->renderAjax('update', [
                'model' => $model,              // Truyền sản phẩm
                'latestPrices' => $latestPrices, // Truyền danh sách giá
                'productid' => $id,
            ]),
            'footer' =>
                Html::button('Đóng lại', [
                    'class'=>'btn btn-default pull-left',
                    'data-bs-dismiss'=>"modal"
                ]) .
                Html::button('Lưu lại', [
                    'class'=>'btn btn-primary',
                    'type'=>"submit",
                    //'type' => 'button', // đổi từ submit → button để tránh reload form
                    'id' => 'btn-update-price'
                ]),
        ]);
    }

    public function actionUpdate()
    {
       
       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $details = Yii::$app->request->post('details', []);
            $datetime = Yii::$app->request->post('datetime', []);

            foreach ($details as $id => $item) {
                $model = \app\models\ProductPricesUnit::findOne($id);
                if ($model) {
                    // Lấy giá trị số (bỏ ký tự không phải số)
                    //$price = preg_replace('/\D/', '', $item['price']);
                    //$model->price = (int)$price;
                    $model->price = NumberHelper::parseNumberVN($item['price']);

                    // Nếu có datetime tương ứng
                    if (isset($datetime[$id]['datetime'])) {
                        $model->datetime = $datetime[$id]['datetime'];
                    }

                    $model->save(false);
                }
            }
           
            $produceModel = ProductsForm::findOne($model->product_id);
            if (!$produceModel) {
                throw new NotFoundHttpException("Không tìm thấy sản phẩm ID: $id");
            }
            $latestPrices = $produceModel->latestProductPricesUnit;

            // giữ lại modal
            return $this->asJson([
                'forceReload'=>'#crud-datatable-pjax',
                'title' => "Cập nhật giá",
                'content' => $this->renderAjax('update', [
                    'model' => $produceModel,              // Truyền sản phẩm
                    'latestPrices' => $latestPrices, // Truyền danh sách giá
                    'productid' => $id,
                ]),
                'tcontent'=>'Cập nhật thành công!',
                'footer' =>
                    Html::button('Đóng lại', [
                        'class'=>'btn btn-default pull-left',
                        'data-bs-dismiss'=>"modal"
                    ]) .
                    Html::button('Lưu lại', [
                        'class'=>'btn btn-primary',
                        'type'=>"submit",
                        //'type' => 'button', // đổi từ submit → button để tránh reload form
                        'id' => 'btn-update-price'
                    ]),
            ]);

           
            // Luu xong đóng modal
           // return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax','tcontent'=>'Cập nhật thành công!','data-bs-dismiss'=>"modal"];

            // return [
            //     'success' => true,
            //     'message' => 'Đã lưu thành công!'
            // ];
        
        }
    }

}