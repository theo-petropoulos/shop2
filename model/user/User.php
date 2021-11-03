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
                $key1,
                OPENSSL_ZERO_PADDING,
                $iv1
            ));
            $stmt = self::$db->prepare(
                'BEGIN;
                INSERT INTO `clients` (`nom`, `prenom`, `mail`, `telephone`, `password`)
                VALUES (?, ?, ?, ?, ?);
                SELECT @user_id := LAST_INSERT_ID();
                INSERT INTO `ips` (`id_user`, `address`) VALUES (@user_id, ?);
                INSERT INTO `register` (`id_user`, `openssl_iv`, `openssl_key`) VALUES (@user_id, ?, ?);
                INSERT INTO `time` (`id_user`, `openssl_iv`, `openssl_key`) VALUES (@user_id, ?, ?);
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
            $link = URL . 'confirm_register?m=' . $crypted_mail . '&a=1&t=' . $crypted_time . '&o=' . $this->mail;
            require ROOT . 'mailer/mailer.php';
            return 'register_success';
        }
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