<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 22:41
 */

class CartController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->title = 'корзина товаров';
        $this->view = 'cart';
    }

    public function index($data)
    {
        return parent::index($data);
    }


    function removeGoods($get = [])
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
                // авторизован юзер, перекинем на главную
            }

            $_GET['asAjax'] = 1;

            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            Cart::getInstance()->removeGoods($data);

        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            Http::response(400, $response);
        }
        return [];
    }

    static function addGoods($get = [])
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
                // авторизован юзер, перекинем на главную
            }

            $_GET['asAjax'] = 1;

            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            Cart::getInstance()->addGoods($data);

        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            Http::response(400, $response);
        }
        return [];
    }
}