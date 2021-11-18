<?php

class AdminCTRL{

    public function __construct(){
        $f3 = Base::instance();
        require MODEL . 'admin/Admin.php';
        if(!empty($f3->get('action'))){
            if($f3->get('action') === 'admin_login'){
                require VIEW . 'admin/login/form.php';
            }
            elseif($f3->get('action') === 'admin_login_submit'){
                if(!empty($_POST['admin_login']) && $_POST['admin_login'] == 1 && !empty($_POST['login']) && !empty($_POST['password'])){
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
                $admin = new Admin($_GET);
                switch($admin->confirmLogin()){
                    case 'success':
                        header("Refresh:0; url=/shop/admin");
                        break;
                    default:
                        $error = ['origin' => 'admin_login_confirm', 'message' => 'Une erreur est survenue, veuillez vérifier le lien d\'authentification 
                        et contacter l\'équipe technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a> si le problème persiste.'];
                        break;
                }
            }
            elseif($f3->get('action') === 'admin_landing'){
                require VIEW . 'admin/landing/index.php';
            }
            elseif($f3->get('action') === 'modify'){
                switch($_GET['modify']){
                    case 'password':
                        require VIEW . 'admin/modify/password.php';
                        break;
                    case 'clients':
                        echo "<script src='" . SCRIPTS . "admin_clients.js'></script>";
                        require MODEL . 'admin/Manager.php';
                        $manager = new Manager();
                        $clients = $manager->fetchClients();
                        require VIEW . 'admin/modify/clients.php';
                        break;
                    case 'products':
                        echo "<script src='" . SCRIPTS . "admin_products.js'></script>";
                        echo "<script src='" . SCRIPTS . "admin_search_bar.js'></script>";
                        require MODEL . 'admin/Manager.php';
                        $manager = new Manager();
                        $content = $manager->fetchProducts();
                        require VIEW . 'admin/modify/products.php';
                        break;
                    case 'admins':
                        echo "<script src ='" . SCRIPTS . "admin_admins.js'></script>";
                        require MODEL . 'admin/Manager.php';
                        $manager = new Manager();
                        $admins = $manager->fetchAdmins();
                        require VIEW . 'admin/modify/admins.php';
                        break;
                    default:
                        $error = ['origin' => 'admin_modify', 'message' => 'Une erreur inattendue est survenue. Si le problème persiste, 
                        veuillez contacter l\'assistance technique à <a href="mailto:assistance@shop.com">assistance@shop.com</a>.
                        <br>Revenir à l\'<a href="/shop/">Accueil</a>.'];
                        break;
                }
            }
            elseif($f3->get('action') === 'modify_password'){
                $_POST['authtoken'] = $_COOKIE['ADMauthtoken'];
                $admin = new Admin($_POST);
                switch($admin->setNewPassword()){
                    case 'modify_success':
                        $success = 1;
                        require VIEW . 'admin/modify/password.php';
                        break;
                    case 'modify_failure':
                        $error = ['origin' => 'profile_password', 'message' => 'Une erreur est survenue pendant la mise à jour de votre mot de passe. 
                        Veuillez essayer de vous déconnecter / reconnecter, puis renouveller la tentative. Si le problème persiste, veuillez contacter 
                        l\'assistance technique à <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a>.']; 
                        break;
                    case 'invalid_strenght':
                        $error = ['origin' => 'profile_password', 'message' => 'Le mot de passe n\'est pas assez fort. Pour rappel, il doit faire 
                        au moins 8 caractères de long et contenir au moins:<br>
                        - Une lettre majuscule<br>
                        - Une lettre minuscule<br> 
                        - Un chiffre<br>
                        - Un caractère spécial<br>
                        Veuillez <a href="admin?modify=password">réessayer</a>.'];
                        break;
                    case 'invalid_match':
                        $error = ['origin' => 'profile_password', 'message' => 'Les mots de passe ne correspondent pas. 
                        Veuillez <a href="admin?modify=password">réessayer</a>.'];
                        break;
                    case 'invalid_input':
                    default:
                        $error = ['origin' => 'profile_password', 'message' => 'Une erreur inattendue est survenue pendant la modification de votre 
                        mot de passe. Veuillez <a href="admin?modify=password">réessayer</a>.'];
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