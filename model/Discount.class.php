<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 22:28
 */

class Discount
{
    /**
     * Заглушка функции проверки на скидку. Возвращает процент скидки
     *
     * @param int $product_id Расчет скидки по коду товара, если есть
     * @param string $discount_message
     * @return int
     */
    public function checkDiscount(int $product_id = 0, string &$discount_message = ''): int
    {
        //
        $discount_message = 'Перманентная скидка на всё 5%!';
        return 5;
    }
}
