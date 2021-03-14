<?php

class IndexController extends Controller
{
    public $view = 'index';
    public $title;

    function __construct()
    {
        parent::__construct();
    }
	
	//метод, который отправляет в представление информацию в виде переменной content_data = []
	function index($data){
		 return [];
	}
}

//site/index.php?path=index/test/5