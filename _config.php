<?php

define('HOST', 'http://localhost/' . __DIR__ . '/');
define('URL', 'http://localhost/' . basename(getcwd()) . '/');
define('ROOT',  __DIR__ . '/');

define('CONTROLLER', ROOT . 'controller/');
define('VIEW', ROOT . 'view/');
define('MODEL', ROOT . 'model/');

define('ASSETS', ROOT . 'assets/');
define('SCRIPTS', URL . 'scripts/');
define('UPLOADS', ASSETS . 'uploads/');
define('REQUIRES', VIEW . 'requires/');
