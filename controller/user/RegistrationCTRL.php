<?php

class RegistrationCTRL{
    public function __construct(){
        $f3 = Base::instance();
        if(!empty($f3->get('action'))){
            /**
             * If the user wants to register
             */
            if($f3->get('action') === 'register_form'){
                require VIEW . 'user/register/form.php';
            }
            /**
             * If the user sent a register form
             */
            else if($f3->get('action') === 'register_submit'){
                if( !empty($_POST['mail']) && !empty($_POST['password']) && !empty($_POST['cpassword']) && 
                !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['phone'])){
                    $user = new User($_POST);
                    switch($user->subscribe()){
                        case 'invalid_mail':
                            $error = ['origin' => 'register', 'message' => 'L\'adresse mail renseignée est invalide. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                        case 'invalid_password':
                            $error = ['origin' => 'register', 'message' => 'Le mot de passe n\'est pas assez fort. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                        case 'invalid_match':
                            $error = ['origin' => 'register', 'message' => 'Les mots de passe ne correspondent pas. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                        case 'invalid_name':
                            $error = ['origin' => 'register', 'message' => 'Le nom ou le prénom contiennent des caractères interdits. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                        case 'invalid_phone':
                            $error = ['origin' => 'register', 'message' => 'Le numéro de téléphone contient des caractères interdits. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                        case 'user_exists':
                            $error = ['origin' => 'register', 'message' => 'Cette adresse mail et/ou ce numéro de téléphone sont déjà utilisés. Veuillez 
                            <a href="inscription">réessayer</a>.<br><a href="reset-password">Réinitialiser le mot de passe ?</a>'];
                            break;
                        case 'register_success':
                            $confirm = 'pending';
                            break;
                        default:
                            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue durant votre inscription. Veuillez 
                            <a href="inscription">réessayer</a>.'];
                            break;
                    }
                }
            } 
            /**
             * If the user is validating his registration
             */
            else if($f3->get('action') === 'register_confirm'){
                if(filter_var($_GET['o'], FILTER_VALIDATE_EMAIL) && intval($_GET['a']) === 1){
                    $user = new User(['mail' => $_GET['o'], 'crypted_mail' => $_GET['m'], 'crypted_time' => $_GET['t']]);
                    switch($user->confirmRegister()){
                        case 'already_registered':
                            $error = ['origin' => 'register', 'message' => 'Ce lien d\'activation a déjà été utilisé. Vous pouvez maintenant vous 
                            <a href="profil">connecter.</a>'];
                            break;
                        case 'user_not_found':
                        case 'invalid_mail':
                            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue durant votre inscription. Veuillez 
                            réessayer avec le lien contenu dans le mail qui vous a été envoyé.<br>Revenir à l\'<a href="/shop/">Accueil</a>.'];
                            break;
                        case 'invalid_time':
                            $error = ['origin' => 'register', 'message' => 'Le lien d\'activation a expiré. Veuillez contacter l\'assistance technique à 
                            <a href="mailto:assistance@shop.com">assistance@shop.com</a>.<br>Revenir à l\'<a href="/shop/">Accueil</a>.'];
                            break;
                        case 'valid_registration':
                            $confirm = 'success';
                            break;
                        default:
                            $error = ['origin' => 'register', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                            veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                            <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];
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
            <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];

        if(!empty($error)) require VIEW . 'error/generator.php';
        elseif(!empty($confirm)) require VIEW . 'user/register/success.php';
    }
}