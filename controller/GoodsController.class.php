<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 10:31
 */

class GoodsController extends Controller
{
    protected $errors = [];
    public function __construct()
    {
        $this->title = 'Управление товарами';
        $this->view = 'goods';
    }

    public function index($get)
    {
        $data = [
            'goods' => [],
            'good_item' => [],
            'errors' => []
        ];

        if (!App::isAdmin()) {
            $data['errors'][] = 'Страница только для Админа';
            return $data;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->doGoodsItemAction($data['good_item']);
        }

        try {
            $rows = Good::getInstance()->getAll();
            $data['goods'] = $rows;
        } catch (Exception $e) {
            $data['errors'][] = $e->getMessage();
        }
        $data['errors'] = $this->errors;
        return $data;
    }

    /**
     * @param $goods_item
     */
    protected function doGoodsItemAction(&$goods_item)
    {
        try {
            $goods_item['action'] = isset($_POST['action']) ? $_POST['action'] : '';
            $goods_item['id'] = isset($_POST['id']) ? $_POST['id'] : null;
            $goods_item['name'] = isset($_POST['name']) ? $_POST['name'] : null;
            $goods_item['description_short'] = isset($_POST['description_short']) ? $_POST['description_short'] : null;
            $goods_item['property'] = isset($_POST['property']) ? $_POST['property'] : null;
            $goods_item['description'] = isset($_POST['description']) ? $_POST['description'] : null;
            $goods_item['price'] = isset($_POST['price']) ? $_POST['price'] : null;

            if ($goods_item['action'] == 'add') {
                // add new goods_item
                Good::getInstance()->save($goods_item, $this->errors);
                if (!$this->errors) {
                    // сделаем редирект, чтоб не добавить еще один товар при простом обновлении страницы
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
                return;
            }

            if ($goods_item['action'] == 'delete') {
                Good::getInstance()->deleteById($goods_item['id']);
            }
            if ($goods_item['action'] == 'update') {
                Good::getInstance()->save($goods_item);
                $goods_item = [];
            }

            // переход в режим редактирования
            if ($goods_item['action'] == 'edit') {
                $goods_item = Good::getInstance()->getById($goods_item['id']);
                if (!$goods_item)
                    throw new Exception('Товар не найден');
                $goods_item['action'] = 'edit';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
