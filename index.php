<?php
require_once '_config.php';
require_once 'vendor/autoload.php';
$f3 = \Base::instance();
$f3->set('JAR.domain', $_SERVER['HTTP_HOST']);

require_once CONTROLLER . 'data/SessionCTRL.php';

/** 
 * Home page
 */
$f3->route('GET /',
    function($f3){
        $title = 'ACCUEIL';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
        require 'controller/IndexCTRL.php';
        $page = new IndexCTRL();
    }
);

/**
 * Admin
 */
$f3->route('GET /admin',
    function($f3, $params){
        $title = 'ADMINISTRATION';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
    }
);

/**
 * Users
 */
$f3->route('GET /profil',
    function($f3){        
        $session = new Session();
        if($session->authenticate()){
            $title = 'PROFIL';
            require_once REQUIRES . 'head.php';
            require_once REQUIRES . 'header.php';
            require MODEL . 'user/Profile.php';
            $profile = new Profile(['authtoken' => $_COOKIE['authtoken']]);
        }
        else{
            $f3->reroute('/connexion');
        }
        // else{
        //     $title = 'CONNEXION';
        //     require_once REQUIRES . 'head.php';
        //     require_once REQUIRES . 'header.php';
        //     $f3->set('action', 'login_form');
        //     require CONTROLLER . 'user/ConnectionCTRL.php';
        //     $page = new ConnectionCTRL();
        // }
    }
);

$f3->route('POST /profil',
    function($f3){
        $title = 'CONNEXION';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
        $f3->set('action', 'login_submit');
        require CONTROLLER . 'user/ConnexionCTRL.php';
        $page = new ConnexionCTRL();
    }
);

$f3->route('GET /inscription*',
    function($f3){
        $title = 'INSCRIPTION';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
        $session = new Session();
        if($session->authenticate()){
            echo "already connected";
        }
        else{
            if(!empty($_GET['o']) && !empty($_GET['m']) && !empty($_GET['a']) && !empty($_GET['t']))
                $f3->set('action', 'register_confirm');
            else $f3->set('action', 'register_form');
            require CONTROLLER . 'user/RegistrationCTRL.php';
            $page = new RegistrationCTRL();
        }
    }
);

$f3->route('POST /inscription',
    function($f3){
        $title = 'INSCRIPTION';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
        $f3->set('action', 'register_submit');
        require CONTROLLER . 'user/RegistrationCTRL.php';
        $page = new RegistrationCTRL();
    }
);

$f3->route('GET /connexion*',
    function($f3){
        $title = 'CONNEXION';
        require_once REQUIRES . 'head.php';
        require_once REQUIRES . 'header.php';
        if(empty($f3['GET']))
            $f3->set('action', 'login_form');
        else
            $f3->set('action', 'login_confirm');
        require CONTROLLER . 'user/ConnexionCTRL.php';
        $page = new ConnexionCTRL();
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

require_once REQUIRES . 'footer.php';
