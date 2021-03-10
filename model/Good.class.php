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
        return DB::getInstance()->QueryMany("SELECT * from goods order by `name`");
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getById($id) {
        return DB::getInstance()->QueryOne("SELECT * from goods WHERE id=?", $id);
    }

    /**
     * Обновить товар
     *
     * @param array $goods_item
     * @throws Exception
     */
    function update($goods_item)
    {
// добавление товара нужно больше проверок. Например на неотрицательность цены))
        if (empty($goods_item['id'])) throw new Exception('Не заполнен код');
        if (empty($goods_item['name'])) throw new Exception('Не заполнено наименование');

// "очищать" входные данные не будем, пусть этим БД займется при выполнении запросов
        DB::getInstance()->QueryOne("update goods set 
                                      `name`=?
                                    , `description_short`=?
                                    , `property`=?
                                    , `description`=?
                                    , `price`=?
                                     where id=?",
            $goods_item['name'],
            $goods_item['description_short'],
            $goods_item['property'],
            $goods_item['description'],
            $goods_item['price'],
            $goods_item['id']);
    }

    /**
     * Добавить товар
     *
     * @param array $goods_item
     * @throws Exception
     */
    function add($goods_item)
    {
        if (empty($goods_item['name'])) throw new Exception('Не заполнено наименование');

        DB::getInstance()->QueryOne("insert into goods set 
                                      `name`=?
                                    , `description_short`=?
                                    , `property`=?
                                    , `description`=?
                                    , `price`=?",
            $goods_item['name'],
            $goods_item['description_short'],
            $goods_item['property'],
            $goods_item['description'],
            $goods_item['price']);
    }

    /**
     * Удаление товара
     *
     * @param $goods_item
     * @throws Exception
     */
    function delete($goods_item)
    {
        if (!$goods_item['id'])
            throw new Exception('Код удаляемого товара не задан');

        DB::getInstance()->QueryOne("delete from goods where id=? limit 1", $goods_item['id']);
    }

}