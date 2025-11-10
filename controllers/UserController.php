<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use yii\web\Response;

class UserController extends Controller
{
    // Bật trả về JSON
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * POST api/user/login
     */
    public function actionLogin()
    {
       
        $request = Yii::$app->request;
        $body = json_decode($request->getRawBody(), true);

        /* return [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'id' => $body['username'],
                'username' => $body['username'],
                'password' => $body['password'],
                // token có thể thêm nếu dùng JWT
            ],
        ]; */

        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;

        if (!$username || !$password) {
            return [
                'success' => false,
                'message' => 'Tên đăng nhập và mật khẩu không được để trống',
            ];
        }

        // Tìm user theo email
        $user = User::findOne(['username' => $username]);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Người dùng không tồn tại',
            ];
        }

        // Kiểm tra password (giả sử bạn hash bằng bcrypt)
        if (!Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            return [
                'success' => false,
                'message' => 'Mật khẩu không đúng',
            ];
        }

        // Nếu thành công, trả về thông tin user
        return [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                // token có thể thêm nếu dùng JWT
            ],
        ];
    }
}
