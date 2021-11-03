<?php

    class Profile extends Database{

        public function __construct(){
            $f3 = Base::instance();
            if(!empty($f3->get('action'))){
                if($f3->get('action') === 'register_form'){
                    require VIEW . 'user/register.php';
                }
                else if($f3->get('action') === 'register_submit'){
                    if( !empty($_POST['mail']) && !empty($_POST['password']) && !empty($_POST['cpassword']) && 
                    !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['phone'])){
                        $user = new User($_POST);
                        switch($user->subscribe()){
                            case 'invalid_mail':
                                $error = ['origin' => 'register', 'message' => 'L\'adresse mail renseignée est invalide. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'invalid_password':
                                $error = ['origin' => 'register', 'message' => 'Le mot de passe n\'est pas assez fort. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'invalid_match':
                                $error = ['origin' => 'register', 'message' => 'Les mots de passe ne correspondent pas. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'invalid_name':
                                $error = ['origin' => 'register', 'message' => 'Le nom ou le prénom contiennent des caractères interdits. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'invalid_phone':
                                $error = ['origin' => 'register', 'message' => 'Le numéro de téléphone contient des caractères interdits. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'user_exists':
                                $error = ['origin' => 'register', 'message' => 'Cette adresse mail et/ou ce numéro de téléphone sont déjà utilisés. Veuillez 
                                <a href="inscription">réessayer</a>.<br><a href="reset-password">Réinitiliser le mot de passe ?</a>'];
                                require VIEW . 'error/error.php';
                                break;
                            case 'register_success':
                                // $crypttime = urlencode(openssl_encrypt(time(), $cipher, $key2, OPENSSL_ZERO_PADDING, $iv));
                                
                                $register_return = "L'inscription a bien été enregistrée. Un e-mail de confirmation va vous être envoyé.<br><a href='" . URL . "'>Accueil</a>";
                                break;
                            default:
                                $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue durant votre inscription. Veuillez 
                                <a href="inscription">réessayer</a>.'];
                                require VIEW . 'error/error.php';
                                break;
                        }
                    }
                }     
                else{
                    $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue durant votre inscription. Veuillez 
                    <a href="inscription">réessayer</a>.'];
                    require VIEW . 'error/error.php';
                }
            }
            else{
                require VIEW . 'user/login.php';
            }
        }
    }