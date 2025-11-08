<?php

namespace app\modules\alsystems\controllers;

use app\models\AlPricingTable;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\alsystems\models\AlSystemsForm;
use app\modules\alsystems\models\AlSystemsSearch;
use app\models\AlSystems;
use app\models\AlProfiles;
use app\modules\alprofiles\models\AlProfilesForm;

/**
 * DefaultController implements CRUD actions for AlSystemsForm model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
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
                    'bulkdelete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AlSystemsForm models.
     */
    public function actionIndex()
    {
        $searchModel = new AlSystemsSearch();

        if (isset($_POST['search']) && $_POST['search'] !== null) {
            $dataProvider = $searchModel->search(Yii::$app->request->post(), $_POST['search']);
        } elseif ($searchModel->load(Yii::$app->request->post())) {
            $searchModel = new AlSystemsSearch(); // reset
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
     * Displays a single AlSystemsForm model.
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Xem hệ nhôm",
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                          . Html::a('Sửa', ['update','id'=>$id], ['class'=>'btn btn-primary','role'=>'modal-remote']),
            ];
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AlSystemsForm model.
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new AlSystemsForm();
        $profiles = [new AlProfilesForm()];

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Hiển thị form lần đầu
            if ($request->isGet) {
                return [
                    'title' => 'Thêm mới hệ nhôm',
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                        'profiles' => $profiles,
                    ]),
                    'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                              . Html::button('Lưu lại', ['class'=>'btn btn-primary','type'=>"submit"]),
                ];
            }

            // Submit form
            if ($model->load($request->post()) && $model->validate()) {
                $result = $this->saveSystemProfiles($model);

                // ✅ Nếu thêm thành công
                if ($result['status'] === 1) {
                    return [
                        'forceClose' => true,
                        'forceReload' => '#crud-datatable-pjax',
                        'content' => $this->renderAjax('create', [
                            'model' => $model,
                            'profiles' => $profiles,
                        ]),
                        'tcontent' => $result['message'], // 👈 hiển thị SweetAlert
                        'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                                    . Html::button('Lưu lại', ['class'=>'btn btn-primary','type'=>"submit"]),
                    ];
                }

                // ❌ Nếu lỗi
                return [
                    'title' => 'Thêm mới hệ nhôm',
                    'content' => '<span class="text-danger">' . Html::encode($result['message']) . '</span>',
                    'forceClose' => false,
                    'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                            . Html::a('Thêm mới', ['create'], ['class'=>'btn btn-primary','role'=>'modal-remote']),
                ];
            }

            // Nếu validate lỗi
            return [
                'title' => 'Thêm mới hệ nhôm',
                'content' => $this->renderAjax('create', [
                    'model' => $model,
                    'profiles' => $profiles,
                ]),
                'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                          . Html::button('Lưu lại', ['class'=>'btn btn-primary','type'=>"submit"]),
            ];
        }

        // Non-AJAX
        if ($model->load($request->post()) && $model->validate()) {
            $result = $this->saveSystemProfiles($model);

            if ($result['status'] === 1) {
                Yii::$app->session->setFlash('success', $result['message']);
                return $this->redirect(['view', 'id' => $result['id']]);
            }

            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->render('create', [
            'model' => $model,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Lưu hệ nhôm và các thanh nhôm liên quan.
     */
    protected function saveSystemProfiles($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Kiểm tra trùng mã hệ nhôm
            $query = AlSystems::find()->where(['code' => $model->code]);
            if (!empty($model->id)) {
                $query->andWhere(['<>', 'id', $model->id]);
            }
            if ($query->exists()) {
                return ['status'=>0,'message'=>'⚠️ Mã hệ nhôm đã tồn tại.'];
            }

            // Lưu hệ nhôm
            $system = $model->id ? AlSystems::findOne($model->id) : new AlSystems();
            $system->setAttributes($model->attributes);
            $now = date('Y-m-d H:i:s');
            if ($system->isNewRecord) $system->created_at = $now;
            $system->updated_at = $now;

            $system->status = $model->status == 1 ? 'Active' : 'Inactive';

            if (!$system->save()) {
                $transaction->rollBack();
                return ['status'=>0,'message'=>'❌ Lỗi lưu hệ nhôm','errors'=>$system->getErrors()];
            }

            // Lưu các thanh nhôm
            $profiles = Yii::$app->request->post('alProfiles', []);
            foreach ($profiles as $p) {
                if (empty($p['code']) && empty($p['name'])) continue;

                $profile = new AlProfiles();
                $profile->system_id = $system->id;
                $profile->code = $p['code'] ?? '';
                $profile->name = $p['name'] ?? '';
                $profile->door_types = $p['door_types'] ?? '';
                $profile->length = (float)str_replace('.', '', $p['length'] ?? 0);
                $profile->weight_per_meter = (float)str_replace('.', '', $p['weight_per_meter'] ?? 0);
                $profile->unit_price = (float)str_replace('.', '', $p['unit_price'] ?? 0);
                $profile->image_url = $p['image_url'] ?? '';
                $profile->status = $p['status'] ?? 'inactive';
                $profile->note = $p['note'] ?? '';
                $profile->created_at = $now;
                $profile->updated_at = $now;

                if (!$profile->save()) {
                    throw new \Exception('Lỗi lưu thanh nhôm: ' . json_encode($profile->getErrors(), JSON_UNESCAPED_UNICODE));
                }
            }

            $transaction->commit();
            return ['status'=>1, 'id'=>$system->id, 'message'=>'✅ Lưu hệ nhôm và các thanh nhôm thành công.'];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['status'=>0, 'message'=>'❌ Lưu không thành công: ' . $e->getMessage()];
        }
    }

    /**
     * Updates an existing AlSystemsForm model.
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $profiles = $model->alProfiles;
        
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                
                return [
                    'title' => 'Cập nhật hệ nhôm',
                    'content' => $this->renderAjax('update', [
                        'model'=>$model,
                        'profiles' => $profiles
                    ]),
                    'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                              . Html::button('Cập nhật', ['class'=>'btn btn-primary','type'=>"submit"]),
                ];
            }

            if ($model->load($request->post()) && $model->validate()) {

                $profilesPost = Yii::$app->request->post('alProfiles', []);
                //dd($profilesPost);
                $result = $this->updateSystemAndProfiles($model, $profilesPost);

                if ($result['status'] === 1) {
                    // cập nhật thành công
                    return [
                        'forceClose' => true,
                        'forceReload' => '#crud-datatable-pjax',
                        'tcontent' => $result['message'],
                    ];
                } else {
                    // cập nhật thất bại
                    return [
                        'title' => 'Cập nhật hệ nhôm',
                        'forceClose' => false,
                        'forceReload' => '#crud-datatable-pjax',
                        'content' => $this->renderAjax('update', [
                            'model'=>$model,
                            'profiles' => $profiles
                        ]),
                        'tcontent' => $result['message'],
                         'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                              . Html::button('Cập nhật', ['class'=>'btn btn-primary','type'=>"submit"]),
                    ];
                }
            }

            return [
                'title' => 'Cập nhật hệ nhôm',
                'content' => $this->renderAjax('update', [
                    'model'=>$model,
                    'profiles' => $profiles
                ]),
                'tcontent' => Html::errorSummary($model),
                'footer' => Html::button('Đóng', ['class'=>'btn btn-secondary','data-bs-dismiss'=>"modal"])
                          . Html::button('Cập nhật', ['class'=>'btn btn-primary','type'=>"submit"]),
            ];
        }

        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['view', 'id'=>$model->id]);
        }

        return 
            $this->render('update', [
                'model'=>$model,
                'profiles' => $profiles
            ]);
    }

    /**
     * Cập nhật hệ nhôm và các thanh nhôm liên quan.
     */
  protected function updateSystemAndProfiles($model, $profilesPost)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');

            // --- 1️⃣ Cập nhật hoặc tạo mới hệ nhôm ---
            $system = $model->id ? AlSystems::findOne($model->id) : new AlSystems();
            $system->setAttributes($model->attributes);
            if ($system->isNewRecord) $system->created_at = $now;
            $system->updated_at = $now;

            $system->status = $model->status == 1 ? 'Active' : 'Inactive';
            //dd($system);
            if (!$system->save()) {
                throw new \Exception('❌ Lỗi lưu hệ nhôm: ' . json_encode($system->getErrors(), JSON_UNESCAPED_UNICODE));
            }

            $systemId = $system->id;

            // --- 2️⃣ Lấy danh sách profile hiện có trong DB ---
            $existingProfiles = AlProfiles::find()
                ->where(['system_id' => $systemId])
                ->indexBy('id')
                ->all();

            // Danh sách ID profile có trong form
            $submittedIds = [];
            foreach ($profilesPost as $p) {
                if (!empty($p['id'])) $submittedIds[] = $p['id'];
            }
            //dd($profilesPost);
            // --- 3️⃣ Thêm mới hoặc cập nhật profile ---
            foreach ($profilesPost as $p) {
                // Bỏ qua dòng trống (không code và name)
                if (empty($p['code']) && empty($p['name'])) continue;
                
                if (!empty($p['id']) && isset($existingProfiles[$p['id']])) {
                    // Cập nhật profile cũ
                    $profile = $existingProfiles[$p['id']];
                } else {
                    // Thêm mới profile
                    $profile = new AlProfiles();
                    $profile->system_id = $systemId;
                    $profile->created_at = $now;
                }

                $profile->code = $p['code'] ?? '';
                $profile->name = $p['name'] ?? '';
                $profile->door_types = $p['door_types'] ?? '';
                $profile->length = (float)str_replace('.', '', $p['length'] ?? 0);
                $profile->weight_per_meter = (float)str_replace('.', '', $p['weight_per_meter'] ?? 0);
                $profile->unit_price = (float)str_replace('.', '', $p['unit_price'] ?? 0);
                $profile->image_url = $p['image_url'] ?? '';
                $profile->status = $p['status'] ?? 'inactive';
                $profile->note = $p['note'] ?? '';
                $profile->updated_at = $now;

                //dd($profile);

                if (!$profile->save()) {
                    throw new \Exception('❌ Lỗi lưu profile: ' . json_encode($profile->getErrors(), JSON_UNESCAPED_UNICODE));
                }
            }

            // --- 4️⃣ Xóa profile bị loại bỏ nếu không có reference ---
            $allProfileIds = array_map('strval', array_keys($existingProfiles));
            $submittedIds = array_map('strval', $submittedIds);
            $toDeleteIds = array_diff($allProfileIds, $submittedIds);

            //dd($toDeleteIds);

            foreach ($toDeleteIds as $id) {
                $pricingExists = (new \yii\db\Query())
                    ->from('al_pricing_table')
                    ->where(['profile_id' => $id])
                    ->exists();

                $materialsExists = (new \yii\db\Query())
                    ->from('al_aluminum_materials')
                    ->where(['profile_id' => $id])
                    ->exists();

                if (!$pricingExists && !$materialsExists) {
                    // xóa ActiveRecord để trigger behaviors nếu có
                    $profile = AlProfiles::findOne($id);
                    if ($profile) $profile->delete();
                }
            }
    
            $transaction->commit();
            return [
                'status' => 1,
                'system_id' => $systemId,
                'message' => '✅ Cập nhật hệ nhôm và Profiles thành công.'
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return [
                'status' => 0,
                'message' => '❌ Lỗi: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Deletes an existing AlSystemsForm model.
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $profile = AlProfilesForm::find()
            ->select('system_id')
            ->where(['system_id' => $id])
            ->all();

        if ($profile) {
            // ❌ Có ràng buộc → Không cho xóa
            return [
                'forceClose' => true,
                'tcontent' => '⚠️ Hệ nhôm đã có thanh nhôm, bạn không thể xóa!',
            ];
        }    

         // ✅ Không có liên kết → Xóa
        $model->delete();

        if ($request->isAjax) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'tcontent' => '✅ Đã xóa hệ nhôm thành công!',
            ];
        }

        return $this->redirect(['index']);

    }

    /**
     * Deletes multiple AlSystemsForm models.
     */
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks', ''));
        $failed = [];

        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
            try {
                $model->delete();
            } catch (\Exception $e) {
                $failed[] = $model->id;
            }
        }

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'forceClose'=>true,
                'forceReload'=>'#crud-datatable-pjax',
                'tcontent'=> empty($failed) ? 'Xóa thành công!' : 'Không thể xóa: ' . implode(', ', $failed),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the AlSystemsForm model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = AlSystemsForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Trang yêu cầu không tồn tại.');
    }
}
