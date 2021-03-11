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

    public function index($data = [])
    {
        $data['orders'] = Order::getInstance()->getOrders(); // массив с информацией об оформленных заказах
        $data['order_status'] = OrderStatus::getInstance()->getAll(); // массив с информацией осостояниях заказа
        return $data;
    }

    public function get()
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $order_id = $data['order_id'];

            $response['data'] = Order::getInstance()->getById($order_id);
            $response['data']['detail'] = OrderDetail::getInstance()->getByOrderId($order_id);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return $response;
    }

    public function update()
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            Order::getInstance()->save($data);

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }

    public function create_from_cart($get = [])
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception('Не верный метод');
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            // Корзина текущего пользователя со скидками
            // и одновременным отфильтровыванием отсутствующих товаров
            $goodsInCart = Cart::getInstance()->getGoodsInCartApplyDiscount();
            if (!$goodsInCart) throw new Exception('Корзина пуста!');

            // сумма всего заказа
            $total_vsego = array_reduce($goodsInCart, function ($a, $b) {
                return $a + $b['vsego'];
            }, 0.0);

            // запись заказа
            DB::getInstance()->StartTransaction();
            $order = [
                'vsego' => $total_vsego,
                'user_id' => User::getInstance()->getUserId(),
                'order_date' => date("Y-m-d H:i:s"),
                'status_id' => OrderStatus::New // Новый заказ
            ];
            $errors = [];
            Order::getInstance()->save($order, $errors);
            if ($errors) {
                throw new Exception(implode(', ', $errors));
            }

            // подробности заказа
            foreach ($goodsInCart as $good) {
                // сохраним позицию в детализации заказа
                $cartId = $good['id'];
                $good['id'] = ''; // очистка, чтобы инициировать вставку новой записи, а не обновление по id
                $good['order_id'] = $order['id'];
                OrderDetail::getInstance()->save($good, $errors);
                if ($errors) {
                    throw new Exception(implode(', ', $errors));
                }

                // очистим корзину от сохраненой позиции
                Cart::getInstance()->deleteById($cartId);
            }
            DB::getInstance()->CommitTransaction();
        } catch (Exception $e) {
            DB::getInstance()->RollbackTransaction();
            return ['error' => $e->getMessage()];
        }
        return [];
    }

    /**
     * Отмена заказа
     *
     * @return array
     */
    public function cancel() {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAuthorized()) {
                throw new Exception('Не авторизован');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            $order = Order::getInstance()->getById($data['id']);
            if (!$order) {
                throw new Exception('Заказ не найден');
            }
            if ($order['status_id'] != OrderStatus::Cancelled) {
                $order['status_id'] = OrderStatus::Cancelled;
                Order::getInstance()->save($order);
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }
}
