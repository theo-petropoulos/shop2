<?php

class ProduitsCTRL{
    
    public function __construct(){
        $f3 = Base::instance();
        if(!empty($f3->get('action'))){
            if($f3->get('action') === 'show_all'){
                require MODEL . 'product/PublicManager.php';
                $manager = new PublicManager();
                $content = $manager->fetchProducts();
                require VIEW . 'products/catalog.php';
            }
        }
        else   
            $error = ['origin' => 'connect', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
            veuillez contacter l\'assistance technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>.
            <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];

        if(!empty($error)) require VIEW . 'error/generator.php';
    }
}