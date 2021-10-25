<?php

require 'vendor/autoload.php';
$f3 = \Base::instance();

$f3->route('GET /',
    function(){
        echo 'Hello, world!';
    }
);

$f3->route('GET /about',
    function(){
        require 'views/about.php';
    }
);

$f3->route('GET /brew/@count',
    function($f3, $params){
        echo 'Ceci est l\'item n°' . $params['count'];
    }
);

$f3->run();