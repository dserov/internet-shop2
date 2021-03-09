<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 09.03.2021
 * Time: 15:38
 */

class User
{
    static $_instance;
    private $user = [];

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->user = [
            'id' => 0,
            'login' => '',
            'is_admin' => 0
        ];
    }

    private function __clone()
    {
    }

    /**
     *
     * @param string $message
     * @return bool
     * @throws Exception
     */
    public function checkAuth(&$message = '')
    {
        if ($this->checkAuthLogin($message)) {
            return true;
        }

        return $this->checkAuthPost($message);
    }

    private function assignUser($row)
    {
        $this->user['id'] = $row['id'];
        $this->user['login'] = $row['login'];
        $this->user['fio'] = $row['fio'];
        $this->user['is_admin'] = $row['is_admin'];
    }

    /**
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Проверка логина из сессии
     *
     * @param string $message
     * @return bool
     * @throws Exception
     */
    public function checkAuthLogin(&$message = '')
    {
        if (isset($_SESSION['login']) && isset($_SESSION['password'])) {
            $row = DB::getInstance()->QueryOne("SELECT * FROM users WHERE login=? and password=? LIMIT 1;", $_SESSION['login'], $_SESSION['password']);
            if (!$row) {
                session_destroy();
            } else {
                $this->assignUser($row);
                return true;
            }
        }
        $message = 'Не авторизован!';
        return false;
    }

    /**
     * @param $message
     * @return bool
     * @throws Exception
     */
    public function checkAuthPost(&$message)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['login']) && $_POST['login'] != '') {
                $row = DB::getInstance()->QueryOne("SELECT * FROM users WHERE login=? LIMIT 1;", $_POST['login']);
                if (!$row) {
                    $message = "Неверный логин или пароль.";
                    return false;
                } else {
                    if ($_POST['password'] && Config::get('secret_salt') . md5($_POST['password']) . Config::get('secret_salt') == $row['password']) {
                        $_SESSION['login'] = $row['login'];
                        $_SESSION['password'] = $row['password'];
                        $this->assignUser($row);
                        return true;
                    }
                }
            }
            $message = 'Неверный логин или пароль.';
        }
        return false;
    }

    function authRegister()
    {
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getUserById($id) {
        return DB::getInstance()->QueryOne("select * from users where id=? limit 1", $id);
    }

    /**
     * @param $login
     * @return array|bool
     * @throws Exception
     */
    public function getUserByLogin($login) {
        return DB::getInstance()->QueryOne("select * from users where login=? limit 1", $login);
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function insertNewUser($data) {
        DB::getInstance()->QueryOne("insert into users (login, password, fio) values (?,?,?)", $data['login'], $data['password'], $data['fio']);
    }
}
