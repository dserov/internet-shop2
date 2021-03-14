<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 11.03.2021
 * Time: 1:16
 */

class OrderDetail extends Model
{
    use Singleton;

    protected static $table = 'orders_detail';

    /**
     * Получим детализацию заказа
     *
     * @param $order_id
     * @return array
     * @throws Exception
     */
    public function getByOrderId($order_id)
    {
        return DB::getInstance()->QueryMany("SELECT od.*, g.name FROM " . static::$table . " od
                                                      left join goods g on g.id=od.goods_id where order_id=?", $order_id);
    }
}