<?php

class DB
{
    use Singleton;

    /**
     * @var $DBLink PDO
     */
    private $DBLink = FALSE;
    public function Connect($user, $password, $base, $host = 'localhost')
    {
        if ($this->DBLink != FALSE)
            unset($this->DBLink);

        $this->DBLink = new PDO("mysql:host={$host};dbname={$base};charset=UTF8", $user, $password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\';'));
        $this->DBLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Выполняет заданный запрос и вовращает массив значений при удаче,
     * пустой массив, если записей нет и false при ошибке.
     *
     * @param ...
     * @return bool|array
     * @throws Exception
     */
    function QueryMany()
    {
        try {
            if ($this->getInstance()->DBLink == FALSE)
                throw new Exception('Соединение с базой данных не установлено.');

            $args = func_get_args();
            $query = array_shift($args);
            if (defined('SQL_QUERY_LOG')) {
                $line = $query;
                $line = str_replace('%', '%%', $line); // hack
                $query_log = vsprintf(str_replace('?', "%s", $line), array_map("DBEscapeString", $args));
                echo $query_log;
            }
            if (!($rs = $this->getInstance()->DBLink->prepare($query))) {
                throw new Exception('Ошибка prepare');
            }
            if (!$rs->execute($args)) {
                throw new Exception('Ошибка execute');
            }
            if (!preg_match('/^\s*(insert|update|delete)/si', $query) && $rs->columnCount() > 0) {
                $rows = array();
                while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                    if (isset($row['id']))
                        $rows[$row['id']] = $row;
                    else
                        $rows[] = $row;
                }
                return $rows;
            }
            return true;
        } catch (Exception $exc) {
            $DBError = $exc->getMessage();
            if (!$this->getInstance()->DBLink) {
                throw new Exception($DBError);
            }
            if ($this->getInstance()->DBLink->inTransaction()) $this->getInstance()->DBLink->rollBack();
            throw new Exception($DBError);
        }
    }

    /**
     * Выполняет заданный запрос и вовращает единственную строку результат в виде массива,
     * пустой массив, если записей нет и false при ошибке.
     *
     * @param ...
     * @return bool|array
     * @throws Exception
     */
    function QueryOne()
    {
        $args = func_get_args();
        $result = $this->getInstance()->QueryMany(...$args);
        return is_array($result) ? current($result) : $result;
    }

    function LastInsertId() {
        return $this->getInstance()->DBLink->lastInsertId();
    }

    /**
     *  запрос с подстановками приводит к простому текстовому виду
     *  представляет из себя основной запрос. после него может идти еще любое количество параметров для подстановки
     *
     * @param ...
     * @return string подготовленная строка
     */
    function PrepareStatement()
    {
        $args = func_get_args();
        $query = array_shift($args);
        $replace = str_replace('?', "%s", $query);
        $arr = array_map([$this->getInstance(), "EscapeString"], $args);
        return vsprintf($replace, $arr);
    }

    /**
     * @param $Value
     * @return mixed
     * @throws Exception
     */
    function EscapeString($Value)
    {
        if ($this->getInstance()->DBLink == FALSE) {
            throw new Exception('Соединение с базой данных не установлено.');
        }

        $result = $this->getInstance()->DBLink->quote($Value);
        if ($result == FALSE) {
            return $Value;
        }

        return $result;
    }

    function StartTransaction() {
        if (!$this->getInstance()->DBLink->inTransaction()) $this->getInstance()->DBLink->beginTransaction();
    }

    function CommitTransaction() {
        if ($this->getInstance()->DBLink->inTransaction()) $this->getInstance()->DBLink->commit();
    }

    function RollbackTransaction() {
        if ($this->getInstance()->DBLink->inTransaction()) $this->getInstance()->DBLink->rollBack();
    }
}
