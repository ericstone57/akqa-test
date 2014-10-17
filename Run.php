<?php
/**
 * User: ericlee
 * Date: 9/27/14
 * Time: 17:55
 */
require ('vendor/autoload.php');
// assume default timezone
date_default_timezone_set('Asia/Shanghai');

use AKQATest\Booking\Booking;

$booking = new Booking('data.txt');
$booking->output();