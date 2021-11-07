<?php

class Connection extends Database{
    
    public function __construct(){
        $f3 = Base::instance();
        if(!empty($f3->get('action'))){
            /**
             * If the user wants to login
             */
            if($f3->get('action') === 'login_form'){
                require VIEW . 'user/login.php';
            }
            /**
             * If the user sent a login form
             */
            else if($f3->get('action') === 'login_submit'){
                if(count($_POST) === 2 && !empty($_POST['mail']) && !empty($_POST['password']) && filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){
                    $user = new User($_POST);
                    switch($user->login()){
                        case 'login_success':
                            echo "succes";
                            break;
                        case 'new_device':
                            $error = ['origin' => 'login', 'message' => 'Vous venez de vous connecter depuis un nouvel appareil. Pour votre sécurité, 
                            un mail vient de vous être envoyé afin de valider cette connexion.'];
                            break;
                        case 'invalid_login':
                        default:
                            $error = ['origin' => 'login', 'message' => 'Identifiant ou mot de passe incorrect. Veuillez <a href="profil">réessayer</a>.'];
                            break;
                    }      
                }
                else
                    $error = ['origin' => 'login', 'message' => 'Identifiant ou mot de passe incorrect. Veuillez <a href="profil">réessayer</a>.'];
            }
            /**
             * If the user is authentifying a new device
             */
            else if($f3->get('action') === 'login_confirm'){
                if(filter_var($_GET['o'], FILTER_VALIDATE_EMAIL) && intval($_GET['a']) === 1){
                    $user = new User(['mail' => $_GET['o'], 'crypted_mail' => $_GET['m'], 'crypted_time' => $_GET['t']]);
                    switch($user->confirmLogin()){
                        case 'user_not_found':
                        case 'invalid_mail':
                            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue durant votre inscription. Veuillez 
                            réessayer avec le lien contenu dans le mail qui vous a été envoyé.<br>Revenir à l\'<a href="/">Accueil</a>.'];
                            break;
                        case 'invalid_time':
                            $error = ['origin' => 'register', 'message' => 'Le lien d\'activation a expiré. Veuillez contacter l\'assistance technique à 
                            <a href="mailto:assistance@shop.com">assistance@shop.com</a>.<br>Revenir à l\'<a href="/">Accueil</a>.'];
                            break;
                        case 'valid_registration':
                            $confirm = 'success';
                            break;
                        default:
                            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                            veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                            <br>Revenir à l\'<a href="/">Accueil</a>.'];
                            break;
                    }
                }
                else
                    $error = ['origin' => 'register', 'message' => 'Une erreur est survenue pendant l\'activation de votre compte. Veuillez réessayer 
                    en suivant le lien contenu dans le mail qui vous a été envoyé. Si le problème persiste, veuillez contacter l\'assistance technique à 
                    <a href="mailto:assistance@shop.com">assistance@shop.com</a>.'];
            }
        }
        else   
            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
            veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
            <br>Revenir à l\'<a href="/">Accueil</a>.'];

        if(!empty($error)) require VIEW . 'error/generator.php';
    }
}