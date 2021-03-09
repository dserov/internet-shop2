<?php

class PersonalAreaController extends Controller
{
    public $view = 'personal_area';

    function __construct()
    {
        parent::__construct();
        $this->title = 'Личный кабинет пользователя';
    }

    function index($get = []){
        $data['orders'] = Order::getInstance()->getOrders(); // масив с информацией об оформленных заказах
        return $data;
    }

}