<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 22:26
 */

class Cart extends Model
{
    use Singleton;

    protected static $table = 'cart';
    /**
     * @var DB $instance
     */
//    private static $instance = null;

    /**
     * @var array
     */
    private $cart = [];

//    static function getInstance()
//    {
//        if (self::$instance === null) {
//            self::$instance = new self;
//        }
//        return self::$instance;
//    }

    private function __construct()
    {
        if (!App::isUser()) return;
        $this->reReadCart();
    }

//    private function __clone()
//    {
//    }

    /**
     * @return array
     */
    function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Discount $discount_instance
     * @return array
     */
    function getGoodsInCartApplyDiscount(Discount $discount_instance = null): array
    {
        $this->reReadCart();
        $data = [];
        foreach ($this->cart as $good) {
            $good['discount_message'] = '';
            $discount = 0;
            if ($discount_instance) {
                $discount = $discount_instance->checkDiscount($good['goods_id'], $good['discount_message']);
            }
            $good['discount'] = round($good['price'] * $discount / 100, 2);
            $good['itogo'] = $good['price'] - $good['discount'];
            $good['vsego'] = round($good['itogo'] * $good['quantity'], 2);
            $data[] = $good;
        }
        $this->cart = $data;
        return $data;
    }

    private function reReadCart()
    {
        $this->cart = DB::getInstance()->QueryMany("SELECT c.id, c.goods_id, g.name, g.price, c.quantity, c.user_id 
        FROM cart c INNER JOIN goods g ON c.goods_id = g.id where c.user_id=?", User::getInstance()->getUserId());
    }

    /**
     * @param $product_id
     * @return array
     */
    public function productInCart($product_id) {
        return array_filter($this->cart, function ($item) use ($product_id) {
            return $item['goods_id'] == $product_id;
        });
    }

    /**
     * Добавляем продукт в корзину
     *
     * @param array $product
     * @param int $quantity
     * @throws Exception
     */
    public function addProduct($product, $quantity)
    {
        // проверка входных данных
        if (!$product) throw new Exception('Такой продукт не найден');
        if (!is_numeric($quantity) || $quantity <= 0)
            throw new Exception('Количество указано неверно');

        // проверка наличия товара в корзине
        $productInCart = $this->productInCart($product['id']);
        if ($productInCart) {
            $productInCart = current($productInCart);
            // суммирование количества с тем, что уже в корзине
            $productInCart['quantity'] += $quantity;
        } else {
            $productInCart['goods_id'] = $product['id'];
            $productInCart['quantity'] = $quantity;
            $productInCart['user_id'] = User::getInstance()->getUserId();
        }
        $this->save($productInCart);

        $this->reReadCart();
    }
}