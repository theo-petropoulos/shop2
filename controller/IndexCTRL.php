<?php

class IndexCTRL extends Session{

    public function __construct(){
        if(isset($_SESSION)){

        }
        echo "Ceci est l'accueil";
    }
}