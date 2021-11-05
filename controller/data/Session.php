<?php
require MODEL . 'data/Session.php';

if(session_status() !== PHP_SESSION_ACTIVE) session_start();

if(!empty($_COOKIE['authtoken'])){
    $session = new Session($_COOKIE['authtoken']);
}