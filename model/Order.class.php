<?php

class Order extends Model {
    use Singleton;

    protected static $table = 'orders';

    /**
     * Массив доступных заказов. Будут отфильтрованы либо по коду юзера, либо если админ - то все
     *
     * @var array
     */
    private $orders = [];

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->orders;
    }

    private function __construct()
    {
        if (!App::isUser() && !App::isAdmin()) throw new Exception("Для работы с заказами требуется авторизация");
        $this->reReadOrders();
    }

    private function reReadOrders()
    {
        if (App::isUser()) {
            $this->orders = DB::getInstance()->QueryMany("SELECT o.*, os.name as status_name, os.description from orders o 
                        inner join order_status os on o.status_id=os.id
                        where o.user_id=? order by o.order_date desc", User::getInstance()->getUserId());
            return;
        }

        if (App::isAdmin()) {
            $this->orders = DB::getInstance()->QueryMany("SELECT o.*, os.name as status_name, u.login as user_login from orders o 
                                                                inner join order_status os on o.status_id=os.id
                                                                inner join users u on u.id=o.user_id order by o.order_date desc");
        }
    }

    /**
     * Проверяет поля на допустимые значения
     *
     * @param array $order
     * @return array Массив с ошибками
     * @throws Exception
     */
    protected function _checkParameters($order)
    {
        $errors = [];
        // тут можно побездельничать. При неверных ид выругается сама БД, т.к. будет нарушаться ее целостность и консистентность
        return $errors;
    }
}