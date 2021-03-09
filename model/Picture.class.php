<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 23:36
 */

class Picture extends Model
{
    static $_instance;
    private $picture = [];

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
        return DB::getInstance()->QueryMany("SELECT * FROM pictures ORDER BY product_id, id desc");
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getByGoodId($id) {
        return DB::getInstance()->QueryMany("SELECT * FROM pictures WHERE product_id=? ORDER BY product_id, id desc", $id);
    }

}