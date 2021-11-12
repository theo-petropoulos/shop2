<?php

    class ProfileCTRL extends Database{

        public function __construct(){
            $f3 = Base::instance();
            if(!empty($f3->get('action'))){
                $profile = new Profile(['authtoken' => $_COOKIE['authtoken']]);
                if($f3->get('action') === 'profile'){
                    require VIEW . 'user/profile/landing.php';
                }
                elseif($f3->get('action') === 'modify'){
                    switch($_GET['modify']){
                        case 'password':
                            require VIEW . 'user/profile/password.php';
                            break;
                        default:
                            $error = ['origin' => 'profile', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                            veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                            <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];
                            break;
                    }
                }
                elseif($f3->get('action') === 'modify_password'){
                    $user = new User(['authtoken' => $profile->authtoken, 'password' => $_POST['password'], 'cpassword' => $_POST['cpassword']]);
                    switch($user->setNewPassword()){
                        case 'modify_success':
                            $success = 1;
                            require VIEW . 'user/profile/password.php';
                            break;
                        case 'modify_failure':
                            $error = ['origin' => 'profile_password', 'message' => 'Une erreur est survenue pendant la mise à jour de votre mot de passe. 
                            Veuillez essayer de vous déconnecter / reconnecter, puis renouveller la tentative. Si le problème persiste, veuillez contacter 
                            l\'assistance technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>']; 
                            break;
                        case 'invalid_strenght':
                            $error = ['origin' => 'profile_password', 'message' => 'Le mot de passe n\'est pas assez fort. Pour rappel, il doit faire 
                            au moins 8 caractères de long et contenir au moins:<br>
                            - Une lettre majuscule<br>
                            - Une lettre minuscule<br> 
                            - Un chiffre<br>
                            - Un caractère spécial<br>
                            Veuillez <a href="profil?modify=password">réessayer</a>.'];
                            break;
                        case 'invalid_match':
                            $error = ['origin' => 'profile_password', 'message' => 'Les mots de passe ne correspondent pas. 
                            Veuillez <a href="profil?modify=password">réessayer</a>.'];
                            break;
                        case 'invalid_input':
                        default:
                            $error = ['origin' => 'profile_password', 'message' => 'Une erreur inattendue est survenue pendant la modification de votre 
                            mot de passe. Veuillez <a href="profil?modify=password">réessayer</a>.'];
                            break;
                    }
                }
            }
            else   
                $error = ['origin' => 'profile', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];

            if(!empty($error)) require VIEW . 'error/generator.php';
        }
    }