<?php
require_once 'autoload.php';

try{
    App::init();
}
catch (PDOException $e){
    echo "PDO Exception" . $e->getMessage();
    echo '<pre>';
    var_dump($e->getTrace());
    echo '</pre>';
}
catch (Exception $e){
    echo $e->getMessage();
}
