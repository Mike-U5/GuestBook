<?php
require_once('GuestbookController.php');

$host = '127.0.0.1';
$user = 'root';
$pass = '';

$endPoint = new GuestbookController($host, $user, $pass);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $endPoint->get();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $endPoint->post();
}
