<?php
require '_config.php';
require MODEL . 'data/session.php';
require 'vendor/autoload.php';

$f3 = \Base::instance();

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
$f3->route('GET /profile',
    function($f3, $params){
        require 'controller/user/Profile.php';
        $page = new Profile();
    }
);

$f3->route('GET /profile/@user',
    function($f3, $params){
        require 'views/profile.php';
    }
);

/**
 * Products
 */
$f3->route('GET /@marque',
    function($f3, $params){

    }
);

$f3->route('GET /@marque/@product',
    function($f3, $params){

    }
);

$f3->run();