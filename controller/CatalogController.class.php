<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 23:24
 */

class CatalogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->title = 'Каталог товаров';
        $this->view = 'catalog';
    }

    public function index($get)
    {
        $data['thumbnails'] = [];
        $data['goods'] = Good::getInstance()->getAll();
        if ($data['goods']) {
            // нужно всего одно фото.
            $rows = Picture::getInstance()->getAll();
            foreach ($rows as $row) {
                $data['thumbnails'][$row['product_id']] = [
                    'picture_path' => str_replace(DIRECTORY_SEPARATOR, '/', $row['path']) . $row['name'],
                    'picture_thumb' => str_replace(DIRECTORY_SEPARATOR, '/', $row['path'] . THUMBNAIL_DIR) . $row['name']];
            }
        }

        return $data;
    }

}