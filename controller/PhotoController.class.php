<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 11.03.2021
 * Time: 15:04
 */

class PhotoController extends Controller
{
    public $view = 'photo';
    public $title = 'Фотогалерея';
    public $errors = [];

    public function __construct()
    {
    }

    public function index($get = [])
    {
        if (!App::isAdmin()) {
            return ['errors' => ['Только для администратора']];
        }

        $productId = isset($get['id']) ? $get['id'] : 0;

        $data = [];
        $data['product'] = Good::getInstance()->getById($productId);
        $data['gallery'] = $this->getGallery($productId);
        $data['errors'] = [];

        $this->title = 'Фотогалерея ' . $data['product']['name'];
        return $data;
    }

    public function upload() {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAdmin()) {
                throw new Exception('Не админ');
            }

            $this->processFiles($_POST['product_id'], PHOTO_DIR);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
    }

    public function delete()
    {
        try {
            $_GET['asAjax'] = 1;
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return [];
            }

            if (!App::isAdmin()) {
                throw new Exception('Не админ');
            }


            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            $this->deletePicture($data['picture_id']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
        return [];
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
                'height' => THUMBNAIL_HEIGHT,
                'size' => $this->formatBytes($row['size'])
            ];
        }
        return $photos;
    }

    /**
     * Удаление картинки из галереи и из файловой системы
     *
     * @param int $picture_id
     * @throws Exception
     */
    protected function deletePicture($picture_id)
    {
        $picture = Picture::getInstance()->getById($picture_id);
        if (!$picture) {
            throw new Exception("Картинка по коду не найдена!");
        }
        unlink($picture['path'] . $picture['name']);
        unlink($picture['path'] . THUMBNAIL_DIR . $picture['name']);
        Picture::getInstance()->deleteById($picture_id);
    }

    /**
     * Загрузка файла
     *
     * @param $productId
     * @param $uploadDir
     */
    protected function processFiles($productId, $uploadDir)
    {
        foreach ($_FILES as $file) {
            if ($file['error'] != 0) {
                $this->errors[] = sprintf('Файл "%s" не загрузился, код ошибки %d', $file['name'], $file['error']);
                continue;
            }
            $result = $this->processFile($productId, $uploadDir, $file['tmp_name']);
            if (is_string($result)) {
                $this->errors[] = sprintf('Файл "%s" не загрузился, ошибка "%s"', $file['name'], $result);;
            }
        }
    }

    /**
     * @param $productId
     * @param $uploadDir
     * @param $tmpName
     * @return string
     */
    protected function processFile($productId, $uploadDir, $tmpName)
    {
        // генерим уникальное имя
        $extension = $this->getTrueExtension($tmpName);
        if ($extension === false)
            return 'Необрабатываемый тип файла';
        $newName = $this->getUniqueName($uploadDir, $extension);
        if ($newName === false)
            return 'Не удалось получить уникальное имя файла';

        // переносим куда надо
        if (!move_uploaded_file($tmpName, $uploadDir . $newName)) {
            return 'Не удалось переместить/переименовать файл';
        }

        // создадим превьюшку
        if ($this->resizeFile($uploadDir, $newName, $extension) === false)
            return 'Не удалось создать превьюшку';

        // сохраним в БД инфу
        $fileSize = filesize($uploadDir . $newName);
        $picture = [
            'path' => $uploadDir,
            'name' => $newName,
            'size' => (!$fileSize ? 0 : $fileSize),
            'product_id' => $productId,
        ];
        $errors = [];
        Picture::getInstance()->save($picture, $errors);
    }

    /**
     * Форматируем строку с размером
     *
     * @param $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Получение уникального имени файла в заданном каталоге
     *
     * @param string $dir путь, где лежат фотки
     * @param string $ext требуемое расширение
     * @return bool|string
     */
    protected function getUniqueName($dir, $ext)
    {
        $i = 100;
        do {
            $tmpName = mb_substr(uniqid(), 0, 8) . ($ext ? "." . $ext : "");
            $i--;
        } while (file_exists($dir . $tmpName) && $i > 0);
        if ($i <= 0) {
            // за 100 попыток уникальное имя не получили
            return false;
        }
        return $tmpName;
    }

    /**
     * Получим расширение по РЕАЛЬНОМУ содержимому файла
     *
     * @param $tmpName
     * @return bool|string
     */
    protected function getTrueExtension($tmpName)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($tmpName);
        switch ($mimetype) {
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
        }
        return false;
    }

    /**
     * Ресайз картинки. Для упрощения кода не будем проверять существование функций и результат их вызова
     * и НЕ БУДЕМ ЗАМОРАЧИВАТЬСЯ С ГЕОМЕТРИЕЙ
     *
     * @param $uploadDir
     * @param $newName
     * @param $extension
     * @return mixed
     */
    protected function resizeFile($uploadDir, $newName, $extension)
    {
        $functions = [
            'jpg' => ['imagecreatefromjpeg', 'imagejpeg'],
            'png' => ['imagecreatefrompng', 'imagepng'],
            'gif' => ['imagecreatefromgif', 'imagegif'],
        ];
        // make resource
        $image = $functions[$extension][0]($uploadDir . $newName);
        $image = imagescale($image, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
        // save picture
        return $functions[$extension][1]($image, $uploadDir . THUMBNAIL_DIR . $newName);
    }
}
