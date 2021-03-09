<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 22:26
 */

class Cart extends Model
{
    /**
     * @var DB $instance
     */
    private static $instance = null;

    static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Cart();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (!App::isUser()) return;
        $this->reReadCart();
    }

    private function __clone()
    {
    }

    /**
     * @var array
     */
    private $goodsInCart = [];

    /**
     * @return array
     */
    function getGoodsInCart()
    {
        return $this->goodsInCart;
    }

    /**
     * @param Discount $discount_instance
     * @return array
     */
    function getGoodsInCartApplyDiscount(Discount $discount_instance = null): array
    {
        $this->reReadCart();

//        throw new Exception(print_r($this->goodsInCart, true));

        $data = [];
        foreach ($this->goodsInCart as $good) {
            $good['discountMessage'] = '';
            $discount = 0;
            if ($discount_instance) {
                $discount = $discount_instance->checkDiscount($good['goods_id'], $good['discountMessage']);
            }
            $good['discount'] = round($good['price'] * $discount / 100, 2);
            $good['itogo'] = $good['price'] - $good['discount'];
            $good['vsego'] = round($good['itogo'] * $good['quantity'], 2);
            $data[] = $good;
        }
        $this->goodsInCart = $data;
        return $data;
    }

    private function reReadCart()
    {
        $this->goodsInCart = DB::getInstance()->QueryMany("SELECT c.id, c.goods_id, g.name, g.price, c.quantity, c.user_id 
        FROM cart c INNER JOIN goods g ON c.goods_id = g.id where c.user_id=?", User::getInstance()->getUserId());
    }

    public function isAlreadyInCart($good_id) {
        $cart = DB::getInstance()->QueryOne("select id from cart where user_id=? and goods_id=?", User::getInstance()->getUserId(), $good_id);
        return !empty($cart);
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function addGoods($data)
    {
        // проверка входных данных
        $product = DB::getInstance()->QueryOne("select * from goods where id=?", $data['id_product']);
        if (!$product) throw new Exception('Такой продукт не найден');
        if (!is_numeric($data['quantity']) || $data['quantity'] <= 0)
            throw new Exception('Количество указано неверно');

        // проверка наличия товара в корзине
        $goodInCart = array_filter($this->goodsInCart, function ($item) use ($product) {
            return $item['user_id'] == User::getInstance()->getUserId() && $item['goods_id'] == $product['id'];
        });
        $goodInCart = current($goodInCart);
        if ($goodInCart) {
            // установка количества, либо суммирование количества с тем, что уже в корзине
            if (!isset($data['action']) || $data['action'] != 'set') {
                $data['quantity'] += $goodInCart['quantity'];
            }
            DB::getInstance()->QueryOne("UPDATE cart SET quantity=? where id=?", $data['quantity'], $goodInCart['id']);
        } else {
            DB::getInstance()->QueryOne("INSERT INTO cart (goods_id, user_id, quantity) values (?,?,?)",
                $product['id'], User::getInstance()->getUserId(), $data['quantity']);
        }

        $this->reReadCart();
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function removeGoods($data)
    {
        DB::getInstance()->QueryOne("DELETE FROM cart WHERE goods_id=? and user_id=?", $data['id_product'], User::getInstance()->getUserId());
        $this->reReadCart();
    }


}