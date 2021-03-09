<?php

class Order extends Model {
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

    /**
     * @var DB $_instance
     */
    private static $_instance = null;

    static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        if (!App::isUser() && !App::isAdmin()) throw new Exception("Для работы с заказами требуется авторизация");
        $this->reReadOrders();
    }

    private function __clone()
    {
    }

    private function reReadOrders()
    {
        if (App::isUser()) {
            $this->orders = DB::getInstance()->QueryMany("SELECT o.*, os.name as status_name, os.description from orders o 
                        inner join order_status os on o.status_id=os.id
                        where o.user_id=?", User::getInstance()->getUser()['id']);
            return;
        }

        if (App::isAdmin()) {
            $this->orders = DB::getInstance()->QueryMany("SELECT o.*, os.name as status_name, u.login as user_login from orders o 
                                                                inner join order_status os on o.status_id=os.id
                                                                inner join users u on u.id=o.user_id");
        }
    }

    /**
     * Получим детализацию заказа
     *
     * @param $order_id
     * @return array
     * @throws Exception
     */
    public function getOrderDetail($order_id)
    {
        return DB::getInstance()->QueryMany("SELECT od.*, g.name FROM orders_detail od
                                                      left join goods g on g.id=od.goods_id where order_id=?", $order_id);
    }

    /**
     * Сохраняет массив в базу данных. Обновляет поля существующей или добавляет новую запись.
     *
     * @param array $order
     * @param array $errors
     * @throws Exception
     */
    public function save(&$order, &$errors = [])
    {
        // надо сохранить ид. даже пустой.
        $id = @$order['id'];

        // отфильтруем отсутствующие в БД поля и восстановим регистр названий полей
        $this->_restoreFieldNames($order);
        $order['id'] = $id;

        // проверка валидности данных
        $errors = $this->_checkParameters($order);
        if ($errors) return;

        if (!$id) {
            // новый заказ
            self::_saveInsert($order);
        } else {
            // изменение заказа
            self::_saveUpdate($order);
        }
    }

    /**
     * Обновляет поля существующего заказа
     * @param $order
     * @throws Exception
     */
    private function _saveUpdate($order)
    {
        $dbt = $this->getById($order['id']);
        if (!$dbt) {
            throw new Exception('Заказ с кодом ' . $order['id'] . 'либо не существует, либо нет прав доступа.');
        }

        $order = array_merge($dbt, $order);

        $fields = [];
        $params = [];
        foreach ($dbt as $key => $value) {
            if (strcmp($value, $order[$key])) {
                $fields[] = $key . '=?';
                $params[] = $order[$key];
            }
        }

        if (!$fields) return; // нет изменений

        $sql = "UPDATE orders SET " . implode(', ', $fields);
        $sql .= " WHERE id=? LIMIT 1;";
        array_unshift($params, $sql);
        array_push($params, $order['id']);
        DB::getInstance()->QueryOne(...$params);
    }

    /**
     * Создание нового заказа. По идее, прийти могут не все поля. Надо отфильтровать те, которых нет и сохранить остальные.
     *
     * @param $order
     * @throws Exception
     */
    private function _saveInsert(&$order)
    {
        $defaultOrder = [];
        $this->defaultOrder($defaultOrder);
        $order = array_merge($defaultOrder, $order);

        $fields = [];
        $values = [];
        foreach ($defaultOrder as $key => $value) {
            $fields[] = $key . '=?';
            $values[] = $order[$key];
        }
        $sql = "INSERT INTO orders SET " . implode(', ', $fields);
        array_unshift($values, $sql);
        DB::getInstance()->QueryOne(...$values);

        $order['id'] = DB::getInstance()->LastInsertId();
        if (empty($order['id'])) throw new Exception("Ошибка при создании заказа");
    }

    /**
     * Проверяет поля на допустимые значения
     *
     * @param array $order
     * @return array Массив с ошибками
     * @throws Exception
     */
    private function _checkParameters($order)
    {
        $errors = [];
        // тут можно побездельничать. При неверных ид выругается сама БД, т.к. будет нарушаться ее целостность и консистентность
        return $errors;
    }

    /**
     * Функция корректирет имена полей в запросе. Делает так, как в базе данных
     *
     * @param $order
     * @throws Exception
     */
    private function _restoreFieldNames(&$order)
    {
        $data = [];
        $defaultOrder = [];
        $this->defaultOrder($defaultOrder);
        foreach ($defaultOrder as $key => $value) {
            $lowerKey = mb_strtolower($key);
            if (isset($order[$key])) {
                $data[$key] = $order[$key];
            } elseif (isset($order[$lowerKey])) {
                $data[$key] = $order[$lowerKey];
            }
        }
        $order = $data;
    }

    /**
     * Создается пустой массив, содержащий поля из таблицы заказа
     *
     * @param $order
     * @throws Exception
     */
    public function defaultOrder(&$order)
    {
        $order = [];
        // получим поля
        $describe_order = DB::getInstance()->QueryMany("DESCRIBE orders;");
        foreach ($describe_order as $row) {
            if ($row['Field'] === 'id') continue;
            $order[$row['Field']] = (($row['Default'] != '') ? $row['Default'] : '');
        }
    }

    /**
     * Получить заказ в виде массива по коду id
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getById($id)
    {
        $orders = array_filter($this->getOrders(), function ($order) use ($id) {
            return $order['id'] == $id;
        });
        if ($orders) return current($orders);
        return [];
    }

}