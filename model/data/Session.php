<?php
require MODEL . 'data/Database.php';
require MODEL . 'user/User.php';

class Session extends Database{

    public function __construct($authtoken = NULL){
        parent::__construct();
        $this->authtoken = $authtoken;
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function giveCookie(){
        /**
         * Generate hash
         */
        $date = (new DateTime())->getTimeStamp();
        $ip = $_SERVER['REMOTE_ADDR'];
        $start = random_int(1000,9999);
        $end = random_int(1000,9999);
        $token = $start . "-" . $date . ":" . $ip . "+" . $end;
        $iterations = random_int(30000,90000);
        $salt = openssl_random_pseudo_bytes(16);
        $hash = hash_pbkdf2("sha256", $token, $salt, $iterations, 32);
        /**
         * Set cookie for further authentications
         */
        $cookie_options = array(
            'expires' => time() + 36000,
            'path' => '/shop/',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict'
        );
        setcookie('authtoken', $hash, $cookie_options);
        return $hash;
    }
    
    // public function authenticate(){
    //     $stmt = self::$db->prepare(
    //         'SELECT i.address AS `ip` FROM ips i
    //         INNER JOIN users u ON i.id_user=u.id 
    //         WHERE u.authtoken = ?'
    //     );
    //     $stmt->execute([$this->authtoken]);
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     if(isset($result['ip']) && $this->ip == $result['ip']) return 'validtoken';
    //     else return 'invalidtoken';
    // }
}