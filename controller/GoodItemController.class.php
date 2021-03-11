<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 0:24
 */

class GoodItemController extends Controller
{
    public function __construct()
    {
        $this->title = '';
        $this->view = 'good';
    }

    public function index($get)
    {
        $data = [];
        $data['good'] = Good::getInstance()->getById($get['id']);
        $data['isAlreadyInCart'] = !!Cart::getInstance()->productInCart($get['id']); // true, если уже в корзине
        $data['gallery'] = $this->getGallery($get['id']);

        $this->title = $data['good']['name'];

        return $data;
    }

    protected function getGallery($product_id)
    {
        $photos = [];
        $rows = Picture::getInstance()->getByProductId($product_id);
        foreach ($rows as $row) {
            $photos[] = [
                'id' => $row['id'],
                'full' => str_replace(DIRECTORY_SEPARATOR, '/', $row['path']) . $row['name'],
                'thumb' => str_replace(DIRECTORY_SEPARATOR, '/', $row['path'] . THUMBNAIL_DIR) . $row['name'],
                'alt' => $row['alt'],
                'width' => THUMBNAIL_WIDTH,
                'height' => THUMBNAIL_HEIGHT
            ];
        }
        return $photos;
    }
}