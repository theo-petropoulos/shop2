<?php

class User extends Database{

    public function __construct(array $array = NULL){
        parent::__construct();
        if(!empty($array)){
            foreach($array as $item => $value)
                $this->$item = htmlspecialchars($value, ENT_QUOTES);
        }
    }

    public function getHis(string $item){
        return $this->$item ?? null;
    }

    public function subscribe(){
        /**
         * Verify each input
         */
        foreach($this as $item => $value){
            if($item === 'mail')
                if(!filter_var($value, FILTER_VALIDATE_EMAIL))
                    return 'invalid_mail';
            
            if($item === 'password')
                if(strlen($value)<8 || !self::verifyPwd($value))
                    return 'invalid_password';
                
            if($item === 'cpassword')
                if($this->password !== $this->cpassword) 
                    return 'invalid_match';
            
            if($item === 'firstname' || $item === 'lastname'){
                $this->$item = ucfirst($this->$item);
                if(strlen($value) < 2 || strlen($value) > 30 || !preg_match("/^[a-zA-Z\\-\\'âàéèêôîûÂÀÉÈÊ\s]*$/", $value)) 
                    return 'invalid_name';
            }

            if($item === 'phone'){
                $this->phone = str_replace(['-', ',', ' ', '.', '/'], '', $this->phone);
                if(!preg_match("/^[0-9]*$/", $this->phone))
                    return 'invalid_phone';
            }
        }

        /**
         * Verify if mail and/or phone is available
         */
        $stmt = self::$db->prepare(
            'SELECT `id` FROM `clients` 
            WHERE `mail` = ? OR `telephone` = ?;'
        );
        $stmt->execute([$this->mail, $this->phone]);
        if(!empty($stmt->fetch())){
            return 'user_exists';
        }
        /**
         * Insert into db
         */
        else{
            $ip = $_SERVER['REMOTE_ADDR'];
            /**
             * Remove previous entries of this ip address
             */
            $stmt = self::$db->prepare(
                'DELETE FROM `ips`
                WHERE `address` = ?'
            );
            $stmt->execute([$ip]);

            $algo = 'AES-256-CTR';
            /**
             * Generate iv & key for user's mail encryption
             */
            $iv1   = random_bytes(openssl_cipher_iv_length($algo));
            $key1  = openssl_random_pseudo_bytes(64);
            $crypted_mail = urlencode(openssl_encrypt(
                $this->mail,
                $algo,
                $key1,
                OPENSSL_ZERO_PADDING,
                $iv1
            ));

            /**
             * Generate iv & key for time encryption
             */
            $iv2   = random_bytes(openssl_cipher_iv_length($algo));
            $key2  = openssl_random_pseudo_bytes(64);
            $crypted_time = urlencode(openssl_encrypt(
                time(),
                $algo,
                $key2,
                OPENSSL_ZERO_PADDING,
                $iv2
            ));
            $stmt = self::$db->prepare(
                'BEGIN;
                INSERT INTO `clients` (`nom`, `prenom`, `mail`, `telephone`, `password`)
                VALUES (?, ?, ?, ?, ?);
                SELECT @user_id := LAST_INSERT_ID();
                INSERT INTO `ips` (`id_client`, `address`) VALUES (@user_id, ?);
                INSERT INTO `register` (`id_client`, `openssl_iv`, `openssl_key`) VALUES (@user_id, ?, ?);
                INSERT INTO `time` (`id_client`, `openssl_iv`, `openssl_key`) VALUES (@user_id, ?, ?);
                COMMIT;'
            );
            $stmt->execute(
                [$this->lastname, $this->firstname, $this->mail, $this->phone, password_hash($this->password, PASSWORD_DEFAULT), 
                $ip, base64_encode($iv1), base64_encode($key1), base64_encode($iv2), base64_encode($key2)]);
            /**
             * Generate mail's content
             */
            $message = 'register';
            $firstname = $this->firstname;
            $address = $this->mail;
            $link = URL . 'inscription?m=' . $crypted_mail . '&a=1&t=' . $crypted_time . '&o=' . $this->mail;
            require ROOT . 'mailer/mailer.php';
            return 'register_success';
        }
    }

    public function confirmRegister(){
        $stmt = self::$db->prepare(
            'SELECT `id` FROM `clients` WHERE `mail` = ?;'
        );
        $stmt->execute([$this->mail]);
        if(!empty($result = $stmt->fetch(PDO::FETCH_ASSOC))){
            /**
             * Get Keys and IVs
             */
            $stmt = self::$db->prepare(
                'SELECT t.`openssl_iv` AS `time_iv`, t.`openssl_key` AS `time_key`, r.`openssl_iv` AS `register_iv`, r.`openssl_key` AS `register_key` 
                FROM `time` t JOIN `register` r ON t.`id_client` = r.`id_client` 
                WHERE t.`id_client` = ?'
            );
            $stmt->execute([$result['id']]);
            $keychain = $stmt->fetch(PDO::FETCH_ASSOC);
            
            /**
             * Decipher GETs in URL
             */
            $algo = 'AES-256-CTR';
            $time = openssl_decrypt(
                urldecode($_GET['t']),
                $algo,
                base64_decode($keychain['time_key']),
                OPENSSL_ZERO_PADDING,
                base64_decode($keychain['time_iv'])
            );
            $mail = openssl_decrypt(
                urldecode($_GET['m']),
                $algo,
                base64_decode($keychain['register_key']),
                OPENSSL_ZERO_PADDING,
                base64_decode($keychain['register_iv'])
            );

            /**
             * If deciphered mail matches mail origin
             */
            if($mail === $this->mail){
                /**
                 * If it's been less than 1 hour, activate account
                 */
                if(time() - intval($time) < 3600){
                    $stmt = self::$db->prepare(
                        'UPDATE `clients` 
                        SET `active` = 1 
                        WHERE `id` = ?'
                    );
                    $stmt->execute([$result['id']]);
                    return 'valid_registration';
                }
                else return 'invalid_time';
            }
            else return 'invalid_mail';
        }
        else return 'user_not_found';
    }

    public function login(){
        $ip = $_SERVER['REMOTE_ADDR'];
        /**
         * Fetch password and all IPs related to the user
         */
        $stmt = self::$db->prepare(
            'SELECT i.`address`, c.`password` FROM `ips` i 
            INNER JOIN `clients` c ON c.`id` = i.`id_client` 
            WHERE c.`mail` = ? GROUP BY c.`password`, i.`address`'
        );
        $stmt->execute([$this->mail]);
        if($result = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            /**
             * Trim results
             */
            $results = [];
            $results['ip'] = [];
            foreach($result as $key => $pair){
                if(empty($results['password'])) $results['password'] = $pair['password'];
                if(!empty($pair['address'])) array_push($results['ip'], $pair['address']);
            }
            /**
             * Remove devices if there's more than 5 registered
             */
            if(count($results['ip']) > 5){
                for($i = 0; $i < 3; $i++){
                    $stmt = self::$db->prepare(
                        'DELETE FROM `ips` 
                        WHERE `address` = ?'
                    );
                    $stmt->execute([$results['ip'][$i]]);
                }
            }
            if(password_verify($this->password, $results['password'])){
                if(in_array($ip, $results['ip'])){
                    /**
                     * Generate user's token and store its hash into the database
                     */
                    $session = new Session();
                    $this->authtoken = $session->giveCookie();
                    $stmt = self::$db->prepare(
                        'UPDATE `clients` 
                        SET `authtoken` = ? 
                        WHERE `mail` = ?'
                    );
                    $stmt->execute([$this->authtoken, $this->mail]);
                    return 'login_success';
                }
                else{
                    return 'new_device';
                }
            }
        }
        return 'invalid_login';
    }

    protected static function verifyPwd(string $password){
        if( !preg_match('@[A-Z]@', $password) || !preg_match('@[a-z]@', $password) ||
			!preg_match('@[0-9]@', $password) || !preg_match('@[^\w]@', $password) ||
			strlen($password)<8 ){
				return 0;
			}
		else{
			return 1;
		}
    }
}