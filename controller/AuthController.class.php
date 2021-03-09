<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 17:15
 */

class AuthController extends Controller
{
    public $view = 'auth';
    public $title = 'Авторизация пользователя';

    public function login($get = [])
    {
        $data = [];

        $user = User::getInstance()->getUser();

        if ($user['is_admin']) {
            // только что авторизовался админ
            header('Location: ?path=goods');
            exit;
        }

        if (!$user['is_admin'] && $user['id']) {
            // только что авторизовался юзер
            header('Location: ?path=personal_area');
            exit;
        }

        return $data;
    }

    /**
     * Регистрация нового пользователя
     *
     * @param array $get
     * @return array
     */
    public function register($get = [])
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            $user = User::getInstance()->getUser();
            if ($user['id']) {
                // авторизован юзер, перекинем на главную
                header('Location: ?');
                exit;
            }

            $_GET['asAjax'] = 1;

            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            if (!$data['login']) throw new Exception('Пустой логин');
            if (!$data['password']) throw new Exception('Пустой пароль');
            if (!$data['fio']) throw new Exception('Пустое ФИО');

            $user = User::getInstance()->getUserByLogin($data['login']);
            if ($user) throw new Exception('Логин уже занят!');

            $data['password'] = Config::get('secret_salt') . md5($data['password']) . Config::get('secret_salt');
            User::getInstance()->insertNewUser($data);
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            Http::response(400, $response);
        }
        return [];
    }

    public function logout($get = []) {
        session_destroy();
        header('Location: ?');
        exit;
    }
}
