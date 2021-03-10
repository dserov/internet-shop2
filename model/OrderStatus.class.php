<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 11:45
 */

class OrderStatus extends Model
{
    protected static $table = 'order_status';
    static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function getAll() {
        return DB::getInstance()->QueryMany("SELECT * from order_status order by id");
    }
}