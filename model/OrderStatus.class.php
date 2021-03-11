<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 10.03.2021
 * Time: 11:45
 */

class OrderStatus extends Model
{
    use Singleton;
    protected static $table = 'order_status';
    const New = 1;
    const InWay = 2;
    const WaitigForPay = 3;
    const ReadyToDelivery = 4;
    const Completed = 5;
    const Cancelled = 6;
}
