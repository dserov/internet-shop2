<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 0:03
 */

class ContactsController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->title = 'Контакты';
        $this->view = 'contacts';
    }
}