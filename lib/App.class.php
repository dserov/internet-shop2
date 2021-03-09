<?php

class App
{
    public static $user;

    public static function isAdmin() {
        return self::isAuthorized() && self::$user['is_admin'] == '1';
    }

    public static function isUser() {
        return self::isAuthorized() && self::$user['is_admin'] == '0';
    }

    public static function isAuthorized() {
        return isset(self::$user) && self::$user['id'];
    }

    public static function Init()
    {
        session_start();
        date_default_timezone_set('Europe/Moscow');
        db::getInstance()->Connect(Config::get('db_user'), Config::get('db_password'), Config::get('db_base'));

        if (php_sapi_name() !== 'cli' && isset($_SERVER) && isset($_GET)) {
            self::web($_GET['path'] ? $_GET['path'] : '');
        }
    }

    //http://site.ru/index.php?path=News/delete/5


    protected static function web($url)//������!!!
    {
        $url = explode("/", $url);
        if (!empty($url[0])) {
            $_GET['page'] = $url[0];//����� ����� ������ �����������
            if (isset($url[1])) {
                if (is_numeric($url[1])) {
                    $_GET['id'] = $url[1];
                } else {
                    $_GET['action'] = $url[1];//����� ����� ������
                }
                if (isset($url[2])) {//���������� �������� ��� ������ �����������
                    $_GET['id'] = $url[2];
                }
            }
        } else {
            $_GET['page'] = 'index';
        }

        // ����������� ��?
        $auth_message = '';
        User::getInstance()->checkAuth($auth_message);
        self::$user = User::getInstance()->getUser();

        $controllerName = array_reduce(explode('_', $_GET['page']), function ($a, $b) {
                return $a . ucfirst($b);
            }, '') . 'Controller'; //IndexController
        $methodName = isset($_GET['action']) ? $_GET['action'] : 'index';
        $controller = new $controllerName();

        //����� ������� ������� �������� � ����� ������
        //������ data - ��� ������ ��� ������������� � ����� ������
        $data = [
            'content_data' => $controller->$methodName($_GET),
            'page_title' => $controller->title,
            'user' => self::$user,
            'goods_in_cart' => Cart::getInstance()->getGoodsInCartApplyDiscount(),
            'auth_message' => $auth_message
        ];

//        $data['logi'] = print_r($data['content_data']['thumbnails'], true);

        if (isset($_GET['asAjax'])) {
            $result_code = (isset($data['content_data']['error'])) ? 400 : 200;
            Http::response($result_code, $data['content_data']);
        } else {
            $view = $controller->view . '/' . $methodName . '.html';

            $loader = new \Twig\Loader\FilesystemLoader(Config::get('path_templates'));
            $twig = new \Twig\Environment($loader);
            echo $twig->render($view, $data);
        }
    }
}