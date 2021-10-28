<?php

class Index extends Session{

    public function __construct(){
        if(isset($_SESSION)){
            var_dump($_SESSION);
        }
        echo "Ceci est l'accueil";
    }
}