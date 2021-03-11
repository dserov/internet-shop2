<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 23:36
 */

class Picture extends Model
{
    use Singleton;
    protected static $table = 'pictures';

    /**
     * @return array|bool
     * @throws Exception
     */
    public function getAll() {
        return DB::getInstance()->QueryMany("SELECT * FROM " . static::$table . " ORDER BY product_id, id desc");
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getByProductId($id) {
        return DB::getInstance()->QueryMany("SELECT * FROM " . static::$table . " WHERE product_id=? ORDER BY product_id, id", $id);
    }

}