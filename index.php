<?php
require '_config.php';
require MODEL . 'data/session.php';
require 'vendor/autoload.php';

$f3 = \Base::instance();
var_dump($_GET);

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
    function($f3, $params){
        require CONTROLLER . 'user/Profile.php';
        $page = new Profile();
    }
);

$f3->route('GET /inscription',
    function($f3, $params){
        $f3->set('action', 'register_form');
        require CONTROLLER . 'user/Profile.php';
        $page = new Profile();
    }
);

$f3->route('POST /inscription',
    function($f3, $params){
        $f3->set('action', 'register_submit');
        require CONTROLLER . 'user/Profile.php';
        $page = new Profile();
    }
);

$f3->route('GET /confirm_register*',
    function($f3, $params){
        $f3->set('action', 'register_confirm');
        echo "yes";
    }
);

// Access another user's page
// $f3->route('GET /profil/@user',
//     function($f3, $params){
//         require CONTROLLER . 'user/Profile.php';
//         $page = new Profile($params['user']);
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