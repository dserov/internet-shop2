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
        return [];
    }

}