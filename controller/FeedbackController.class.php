<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 0:06
 */

class FeedbackController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->title = 'Отзывы';
        $this->view = 'feedback';
    }
}
