<?php

namespace app\modules\alprofiles\controllers;

use app\models\AlAluminumMaterials;
use app\models\AlPricingTable;
use Yii;
use app\modules\alprofiles\models\AlProfilesForm;
use app\modules\alprofiles\models\AlProfilesSearch;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * DefaultController implements the CRUD actions for AlProfilesForm model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
    		return [
    			'ghost-access'=> [
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

    /**
     * Lists all AlProfilesForm models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new AlProfilesSearch();
  		if(isset($_POST['search']) && $_POST['search'] != null){
            $dataProvider = $searchModel->search(Yii::$app->request->post(), $_POST['search']);
        } else if ($searchModel->load(Yii::$app->request->post())) {
            $searchModel = new AlProfilesSearch(); // "reset"
            $dataProvider = $searchModel->search(Yii::$app->request->post());
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }    
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single AlProfilesForm model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "AlProfilesForm",
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                            Html::a('Sửa',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new AlProfilesForm model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($system_id = null)
    {
        $request = Yii::$app->request;
        $model = new AlProfilesForm();  

        // Nếu URL có system_id thì gán cho model
        if ($system_id !== null) {
            $model->system_id = $system_id;
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Thêm mới",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'system_id' => $system_id, // ✅ Truyền sang view
                    ]),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->validate()) {

                // ✅ Lấy file upload
                $file = UploadedFile::getInstance($model, 'image_url');
                if ($file) {
                    // ✅ Tạo thư mục lưu hình
                    $uploadPath = Yii::getAlias('@webroot/uploads/alprofiles');
                    FileHelper::createDirectory($uploadPath, 0775, true);

                    // ✅ Dùng code của model làm tên file (chuyển về dạng không dấu, lowercase)
                    $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($model->code)); // loại bỏ ký tự đặc biệt
                    $fileName = $safeCode . '.' . $file->extension;
                    $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

                    // ✅ Nếu file trùng tên thì thêm timestamp cho chắc
                    if (file_exists($filePath)) {
                        $fileName = $safeCode . '_' . time() . '.' . $file->extension;
                        $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
                    }

                    // ✅ Lưu file
                    if ($file->saveAs($filePath)) {
                        $model->image_url = 'uploads/alprofiles/' . $fileName; // đường dẫn tương đối
                    }
                }
                //dd($model);
                $model->status = $model->status == 1 ? 'Active' : 'Inactive';
                if($model->save()) {
                    //  return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
                    return [

                        'forceReload'=>'#pjax-profiles-grid-'.$model->system_id,
                        'title'=> "Thêm mới",
                        //'content'=>'<span class="text-success">Thêm mới thành công</span>',
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'system_id' => $system_id, // ✅ Truyền sang view
                        ]),
                        'tcontent'=>'Thêm mới thành công!',
                        'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                                    . Html::button('Lưu lại', ['class'=>'btn btn-primary','type'=>"submit"]),
            
                    ];   
                }         

            }else{           
                return [
                    'title'=> "Thêm mới",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'system_id' => $system_id, // ✅ Truyền sang view
                    ]),
                    'tcontent'=>Html::errorSummary($model),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'system_id' => $system_id, // ✅ Truyền sang view
                ]);
            }
        }
       
    }

    /**
     * Updates an existing AlProfilesForm model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                if ($model->weight_per_meter !== null && $model->weight_per_meter !== '') {
                    $model->weight_per_meter = number_format((float)$model->weight_per_meter, 2, ',', '.');
                }
                if ($model->unit_price !== null && $model->unit_price !== '') {
                    $model->unit_price = number_format((float)$model->unit_price, 2, ',', '.');
                }
                return [
                    'title'=> "Cập nhật",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'system_id' => $model->system_id, // ✅ Truyền sang view
                    ]),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->validate()){
                // ✅ Lưu lại đường dẫn ảnh cũ trước khi cập nhật
                $oldImage = $model->getOldAttribute('image_url');

                // ✅ Lấy file upload mới (nếu có)
                $file = UploadedFile::getInstance($model, 'image_url');

                if ($file) {
                    // ✅ Tạo thư mục lưu hình
                    $uploadPath = Yii::getAlias('@webroot/uploads/alprofiles');
                    FileHelper::createDirectory($uploadPath, 0775, true);

                    // ✅ Làm sạch code để làm tên file an toàn
                    $safeCode = $model->code
                        ? preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($model->code))
                        : 'no_code_' . time();

                    // ✅ Tên file mới
                    $fileName = $safeCode . '.' . strtolower($file->extension);
                    $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

                    // ✅ Nếu file cũ trùng tên => xóa luôn file cũ trước khi lưu
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }

                    // ✅ Lưu file mới
                    if ($file->saveAs($filePath)) {
                        // ✅ Xóa file cũ (nếu khác tên hoặc khác đường dẫn)
                        if (!empty($oldImage)) {
                            $oldFile = Yii::getAlias('@webroot/' . $oldImage);
                            if (file_exists($oldFile) && realpath($oldFile) !== realpath($filePath)) {
                                @unlink($oldFile);
                            }
                        }

                        // ✅ Gán đường dẫn mới cho model
                        $model->image_url = 'uploads/alprofiles/' . $fileName;
                    }
                } else {
                    // ✅ Không upload file mới => giữ nguyên ảnh cũ
                    $model->image_url = $oldImage;
                }   
                $model->status = $model->status == 1 ? 'Active' : 'Inactive';
                //dd($model);
                if($model->save()){
                     return [
                        'forceClose'=>true,
                        'forceReload'=>'#pjax-profiles-grid-'.$model->system_id,
                        'title'=> "Cập nhật",
                        //'content'=>'<span class="text-success">Thêm mới thành công</span>',
                        'content'=>$this->renderAjax('create', [
                            'model' => $model,
                            'system_id' => $model->system_id, // ✅ Truyền sang view
                        ]),
                        'tcontent'=>'Cập nhật thành công!',
                        'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::a('Sửa',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            
                    ];   
                }
            }else{
                 return [
                    'title'=> "Cập nhật",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'system_id' => $model->system_id, // ✅ Truyền sang view
                    ]),
                    'tcontent'=>Html::errorSummary($model),
                    'footer'=> Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'system_id' => $model->system_id, // ✅ Truyền sang view
                ]);
            }
        }
    }

    /**
     * Delete an existing AlProfilesForm model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
   public function actionDelete($id)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

        // ✅ Kiểm tra xem profile có bị ràng buộc trong các bảng khác không
        $hasPricing = AlPricingTable::find()->where(['profile_id' => $id])->exists();
        $hasMaterial = AlAluminumMaterials::find()->where(['profile_id' => $id])->exists();

        if ($hasPricing || $hasMaterial) {
            // ❌ Nếu có ràng buộc thì không cho xóa
            return [
                'forceClose' => true,
                'tcontent' => 'Thanh nhôm này đã được sử dụng trong bảng giá hoặc vật liệu, không thể xóa!',
            ];
        }

        // ✅ Nếu không có ràng buộc thì cho phép xóa
        // => Xóa file ảnh (nếu có)
        if (!empty($model->image_url)) {
            $oldFile = Yii::getAlias('@webroot/' . $model->image_url);
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        // ✅ Thực hiện xóa model
        $model->delete();

       // ✅ Trả về phản hồi Ajax kèm thông báo thành công
        if ($request->isAjax) {
            return [
                'forceClose' => true,
                'forceReload'=>'#pjax-profiles-grid-'.$model->system_id,
                'tcontent' => '✅ Đã xóa thanh nhôm thành công!',
            ];
        }

        // ✅ Nếu không phải Ajax → quay về trang index
        return $this->redirect(['index']);
    }


     /**
     * Delete multiple existing AlProfilesForm model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        $delOk = true;
        $fList = array();
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            try{
            	$model->delete();
            }catch(\Exception $e) {
            	$delOk = false;
            	$fList[] = $model->id;
            }
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax',
            			'tcontent'=>$delOk==true?'Xóa thành công!':('Không thể xóa:'.implode('</br>', $fList)),
            ];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }

    /**
     * Finds the AlProfilesForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AlProfilesForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlProfilesForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
