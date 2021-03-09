<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 21:27
 */

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->title = 'Заказы';
        $this->view = 'orders';
    }

    public function get() {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }

            $_GET['asAjax'] = 1;

            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $order_id = $data['order_id'];

            $response['data'] = Order::getInstance()->getById($order_id);
            $response['data']['detail'] = Order::getInstance()->getOrderDetail($order_id);
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            Http::response(400, $response);
        }
        return $response;
    }

    static function ajaxCreateFromCart()
    {
        $response['result'] = 1;
        try {
            if (!isUser()) throw new Exception("Только для авторизованных пользователей!");

            Orders::getInstance()->createFromCart();
            http_response(200, json_encode($response), 'application/json');
        } catch (Exception $e) {
            $response['result'] = 0;
            $response['errorMessage'] = $e->getMessage();
            http_response(400, json_encode($response), 'application/json');
        }
    }

    /**
     * Создание заказа пользователем
     *
     * @throws Exception
     */
    private function createFromCart()
    {
        // Корзина текущего ползьвателя со скидками
        // и одновременным отфильтровыванием отсутствующих товаров
        $goodsInCart = Cart::getInstance()->getGoodsInCartApplyDiscount(new Discount());

        if (!$goodsInCart) throw new Exception('Корзина пуста!');

        // сумма всего заказа
        $total_vsego = 0.0;
        foreach ($goodsInCart as $good) {
            $total_vsego += $good['vsego'];
        }

        // запись заказа
        DB::getInstance()->StartTransaction();
        $this->defaultOrder($order);
        $order['vsego'] = $total_vsego;
        $order['user_id'] = $_SESSION['user_id'];
        $order['order_date'] = date("Y-m-d H:i:s");
        $order['status_id'] = 1; // Новый заказ
//        DB::getInstance()->QueryOne("INSERT INTO orders set vsego=?, order_date=now(), user_id=?, status_id=1", $total_vsego, $_SESSION['user_id']);
        $this->save($order, $errors);
        $order_id = $order['id'];

        // подробности заказа
        $values = [];
        foreach ($goodsInCart as $good) {
            $values[] = DB::getInstance()->PrepareStatement("(?,?,?,?,?,?,?,?)"
                , $order_id, $good['goods_id']
                , $good['quantity'], $good['price']
                , $good['discount'], $good['itogo']
                , $good['vsego'], $good['discountMessage']);
        }
        DB::getInstance()->QueryOne("INSERT INTO orders_detail (order_id, goods_id, quantity, price, discount, itogo, vsego, discount_message) values " . implode(',', $values));

        // очистим корзину
        DB::getInstance()->QueryOne("DELETE FROM cart WHERE user_id=?", $_SESSION['user_id']);

        DB::getInstance()->CommitTransaction();

        $this->reReadOrders();
    }

    static public function ajaxSaveUpdateOrder($order_id)
    {
        $response['result'] = 1;
        try {
            if (!isUser() && !isAdmin()) throw new Exception("Только для авторизованных пользователей!");

            $json_data = file_get_contents("php://input");
            $order = json_decode($json_data, true);
            $order['id'] = $order_id;

            $errors = [];
            Orders::getInstance()->save($order, $errors);
            if ($errors) throw new Exception(implode('<br>', $errors));

            http_response(200, json_encode($response), 'application/json');
        } catch (Exception $e) {
            $response['result'] = 0;
            $response['errorMessage'] = $e->getMessage();
            http_response(400, json_encode($response), 'application/json');
        }
    }
}
