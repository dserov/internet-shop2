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
        $this->title = 'Корзина товаров';
        $this->view = 'cart';
    }

    public function index($data)
    {
        return [];
    }

    public function update($get=[]) {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception('Ошибка метода');
            }
            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            Cart::getInstance()->save($data);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }

    public function delete($get = [])
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception('Не верный метод');
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            Cart::getInstance()->deleteById($data['id']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }

    public function add_product($get = [])
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception('Не верный метод');
            }

            if (!App::isUser()) {
                throw new Exception('Корзина доступна только юзеру');
                // авторизован юзер, перекинем на главную
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            // проверим, что товар существует
            $product = Good::getInstance()->getById($data['product_id']);
            Cart::getInstance()->addProduct($product, $data['product_quantity']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }
}