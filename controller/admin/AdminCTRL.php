<?php

class AdminCTRL{

    public function __construct(){
        $f3 = Base::instance();
        if(!empty($f3->get('action'))){
            if($f3->get('action') === 'admin_login'){
                require VIEW . 'admin/login/form.php';
            }
            elseif($f3->get('action') === 'admin_login_submit'){
                if(!empty($_POST['admin_login']) && $_POST['admin_login'] == 1 && !empty($_POST['login']) && !empty($_POST['password'])){
                    require MODEL . 'admin/Admin.php';
                    $admin = new Admin($_POST);
                    switch($admin->login()){
                        case 'success':
                            $success = ['origin' => 'admin_login', 'message' => 'Un mail d\'authentification vient de vous être envoyé. La validité de ce mail 
                            est de 5 minutes.'];
                            require VIEW . 'admin/login/form.php';
                            break;
                        case 'failure':
                            $error = ['origin' => 'admin_login', 'message' => 'Une erreur technique est survenue au niveau du système d\'authentification 
                            et/ou de mailing. Veuillez contacter l\'assistnace technique à <a href="mailto:support@minimal-shop.com">.'];
                            break;
                        case 'invalid_login':
                        default:
                            $error = ['origin' => 'admin_login', 'message' => 'Identifiant ou mot de passe incorrect. Veuillez <a href="profil">réessayer</a>.'];
                            break;
                    }      
                }
                else 
                    $error = ['origin' => 'admin_login', 'message' => 'Une erreur inattendue est survenue pendant votre connexion. Veuillez 
                    <a href="admin">réessayer</a>.<br> Si le problème persiste, veuillez contacter l\'assistance technique à 
                    <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>.'];
            }
            elseif($f3->get('action') === 'admin_login_confirm'){
                require MODEL . 'admin/Admin.php';
                $admin = new Admin($_GET);
                switch($admin->confirmLogin()){
                    case 'success':
                        $f3->reroute('admin');
                        break;
                    default:
                        $error = ['origin' => 'admin_login_confirm', 'message' => 'Une erreur est survenue, veuillez vérifier le lien d\'authentification 
                        et contacter l\'équipe technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a> si le problème persiste.'];
                        break;
                }
            }
            else
                $error = ['origin' => 'admin_login', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                veuillez contacter l\'assistance technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>.'];
        }
        else
            $error = ['origin' => 'admin_login', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
            veuillez contacter l\'assistance technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>.'];
        
        if(!empty($error)) require VIEW . 'error/generator.php';
    }
}