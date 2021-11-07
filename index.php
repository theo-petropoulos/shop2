<?php
require '_config.php';
require 'vendor/autoload.php';
$f3 = \Base::instance();
$f3->set('JAR.domain', $_SERVER['HTTP_HOST']);
require CONTROLLER . 'data/Session.php';


/** 
 * Home page
 */
$f3->route('GET /',
    function(){
        require 'controller/Index.php';
        $page = new Index();
    }
);

/**
 * Admin
 */
$f3->route('GET /admin',
    function($f3, $params){

    }
);

/**
 * Users
 */
$f3->route('GET /profil',
    function($f3){
        $session = new Session();
        if($session->authenticate()){
            echo "connected";
        }
        else{
            $f3->set('action', 'login_form');
            require CONTROLLER . 'user/Connection.php';
            $page = new Connection();
        }
    }
);

$f3->route('POST /profil',
    function($f3){
        $f3->set('action', 'login_submit');
        require CONTROLLER . 'user/Connection.php';
        $page = new Connection();
    }
);

$f3->route('GET /inscription*',
    function($f3){
        $session = new Session();
        if($session->authenticate()){
            echo "already connected";
        }
        else{
            if(!empty($_GET['o']) && !empty($_GET['m']) && !empty($_GET['a']) && !empty($_GET['t']))
                $f3->set('action', 'register_confirm');
            else $f3->set('action', 'register_form');
            require CONTROLLER . 'user/Registration.php';
            $page = new Registration();
        }
    }
);

$f3->route('POST /inscription',
    function($f3){
        $f3->set('action', 'register_submit');
        require CONTROLLER . 'user/Registration.php';
        $page = new Registration();
    }
);

$f3->route('GET /connection',
    function($f3){
        $f3->set('action', 'login_confirm');
        require CONTROLLER . 'user/Connection.php';
        $page = new Connection();
    }
);

// $f3->route('GET /confirm_register*',
//     function($f3, $params){
//         $f3->set('action', 'register_confirm');
//         echo "yes";
//     }
// );

/**
 * Products
 */
$f3->route('GET /marques/@marque',
    function($f3, $params){

    }
);

$f3->route('GET /marques/@marque/@product',
    function($f3, $params){

    }
);

$f3->run();