<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 23:36
 */

class Good extends Model
{
    static $_instance;
    private $good = [];

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
        return DB::getInstance()->QueryMany("SELECT * from goods");
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getById($id) {
        return DB::getInstance()->QueryOne("SELECT * from goods WHERE id=?", $id);
    }
}