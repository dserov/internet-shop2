<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 0:06
 */

class FeedbackController extends Controller
{
    protected $errors = [];

    function __construct()
    {
        parent::__construct();
        $this->title = 'Отзывы';
        $this->view = 'feedback';
    }

    public function index($data)
    {
        $data = [
            'reviews' => [],
            'review' => [],
            'errors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->doGoodsItemAction($data['review']);
        }

        try {
            $data['reviews'] = Review::getInstance()->getAll();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        $data['errors'] = $this->errors;
        return $data;
    }

    /**
     * Обработка отзыва
     *
     * @param $review
     * @return string
     */
    protected function doGoodsItemAction(&$review)
    {
        try {
            $review['action'] = isset($_POST['action']) ? $_POST['action'] : '';
            $review['id'] = isset($_POST['id']) ? $_POST['id'] : null;
            $review['author'] = isset($_POST['author']) ? $_POST['author'] : null;
            $review['text'] = isset($_POST['text']) ? $_POST['text'] : null;

            if ($review['action'] == 'add') {
                // add new goods_item
                Review::getInstance()->save($review, $this->errors);
                if (!$this->errors) {
                    // сделаем редирект, чтоб не добавить еще один товар при простом обновлении страницы
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                }
                return;
            }

            if ($review['action'] == 'delete') {
                Review::getInstance()->deleteById($review['id']);
            }
            if ($review['action'] == 'update') {
                Review::getInstance()->save($review);
                $review = [];
            }

            // переход в режим редактирования
            if ($review['action'] == 'edit') {
                $review = Review::getInstance()->getById($review['id']);
                if (!$review)
                    throw new Exception('Отзыв не найден');
                $review['action'] = 'edit';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
