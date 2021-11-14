<?php

class Session extends Database{

    public function __construct($authtoken = NULL){
        parent::__construct();
        $this->authtoken = $authtoken;
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function giveCookie(string $name = NULL){
        /**
         * Generate hash
         */
        $date = (new DateTime())->getTimeStamp();
        $start = random_int(1000,9999);
        $end = random_int(1000,9999);
        $token = $start . "-" . $date . ":" . $this->ip . "+" . $end;
        $iterations = random_int(30000,90000);
        $salt = openssl_random_pseudo_bytes(16);
        $hash = hash_pbkdf2("sha256", $token, $salt, $iterations, 32);
        /**
         * Set cookie for further authentications
         */
        $cookie_options = array(
            'expires' => time() + 36000,
            'path' => '/shop',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict'
        );
        $cookie_name = $name ?? 'authtoken';
        setcookie($cookie_name, $hash, $cookie_options);
        return $hash;
    }
    
    public function authenticate(){
        if(!empty($_COOKIE['authtoken'])){
            $this->authtoken = $_COOKIE['authtoken'];
            $this->ip = $_SERVER['REMOTE_ADDR'];
            $stmt = self::$db->prepare(
                'SELECT i.`address` AS `ip` FROM `ips` i
                INNER JOIN `clients` c ON i.`id_client` = c.`id`
                WHERE c.`authtoken` = ?'
            );
            $stmt->execute([$this->authtoken]);
            if($result = $stmt->fetchAll(PDO::FETCH_ASSOC)){
                foreach($result as $key => $value)
                    if($value['ip'] === $this->ip){
                        self::refreshCookie($this->authtoken);
                        return 1;
                    } 
            }
            setcookie(
                'authtoken',
                '',
                -1,
                '/shop',
                'localhost'
            );
            return 0;
        }
        else return 0;
    }

    public function ADMauthenticate(){
        if(!empty($_COOKIE['ADMauthtoken'])){
            $this->ADMauthtoken = $_COOKIE['ADMauthtoken'];
            $stmt = self::$db->prepare(
                "SELECT `id` FROM `admin` WHERE `authtoken` = ?;"
            );
            $stmt->execute([$this->ADMauthtoken]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                self::refreshCookie($this->ADMauthtoken, 'ADMauthtoken');
                return 1;
            }
            else{
                setcookie(
                    'ADMauthtoken',
                    '',
                    -1,
                    '/shop',
                    'localhost'
                );
                return 0;
            }
        }
        else return 0;
    }

    public function disconnect(string $name = NULL){
        $cookie_name = $name ?? 'authtoken';
        setcookie(
            $cookie_name,
            '',
            -1,
            '/shop',
            'localhost'
        );
    }

    private static function refreshCookie($cookie, string $name = NULL){
        $cookie_options = array(
            'expires' => time() + 36000,
            'path' => '/shop',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict'
        );
        $cookie_name = $name ?? 'authtoken';
        setcookie($cookie_name, $cookie, $cookie_options);
    }
}