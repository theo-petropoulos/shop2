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
            echo "<script src='" . SCRIPTS . "delete_address.js'></script>";
            if(!empty($_GET['disconnect']) && $_GET['disconnect'] == 1){
                $session->disconnect();
                $f3->reroute('/');
            }
            elseif(!empty($_GET['modify']) && in_array($_GET['modify'], ['password', 'orders', 'addresses']))
                $f3->set('action', 'modify');
            elseif(!empty($_GET['delete']) && $_GET['delete'] == 1 && !empty($_GET['confirm']) && $_GET['confirm'] == 1)
                $f3->set('action', 'delete_confirm');
            elseif(!empty($_GET['delete']) && $_GET['delete'] == 1)
                $f3->set('action', 'delete');
            else
                $f3->set('action', 'profile');
            require CONTROLLER . 'user/ProfileCTRL.php';
            $page = new ProfileCTRL();
        }
        else{
            $f3->reroute('/connexion');
        }
    }
);

$f3->route('POST /profil',
    function($f3){
        $keys = array_keys($_POST);
        $i = 0;
        foreach($keys as $k => $key){
            if(str_contains($key, 'modify_'))
                $i++;
        }
        if($i){
            $title = 'PROFIL';
            require_once REQUIRES . 'head.php';
            require_once REQUIRES . 'header.php';
            if(!empty($_POST['modify_password']) && $_POST['modify_password'] == 1)
                $f3->set('action', 'modify_password');
            elseif(!empty($_POST['modify_address']) && $_POST['modify_address'] == 1)
                $f3->set('action', 'modify_address');
            require CONTROLLER . 'user/ProfileCTRL.php';
            $page = new ProfileCTRL();
        }
        else{
            if(!empty($_POST['login']) && $_POST['login'] == 1){
                $title = 'CONNEXION';
                require_once REQUIRES . 'head.php';
                require_once REQUIRES . 'header.php';
                $f3->set('action', 'login_submit');
                require CONTROLLER . 'user/ConnexionCTRL.php';
                $page = new ConnexionCTRL();
            }
        }
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
