<?php

    class ProfilCTRL extends Database{

        public function __construct(){
            $f3 = Base::instance();
            if(!empty($f3->get('action'))){
                
            }
            else   
                $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                <br>Revenir à l\'<a href="/">Accueil</a>.'];

            if(!empty($error)) require VIEW . 'error/generator.php';
            elseif(!empty($confirm)) require VIEW . 'user/register/success.php';
        }
    }