<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 23:36
 */

class Good extends Model
{
    use Singleton;

    static protected $table = 'goods';

    protected function _checkParameters($table)
    {
        $errors = [];
        // добавление товара нужно больше проверок. Например на неотрицательность цены))
        if (empty($goods_item['id'])) {
            $errors[] = 'Не заполнен код';
        }
        if (empty($goods_item['name'])) {
            $errors[] = 'Не заполнено наименование';
        }
        if (!is_float($goods_item['price'])) {
            $errors[] = 'Цена не определена';
        }
        return $errors;
    }
}