<?php

class Admin extends Database{

    public function __construct(array $array = NULL){
        parent::__construct();
        foreach($array as $item => $value)
            $this->$item = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function login(){
        $stmt = self::$db->prepare(
            'SELECT `password` FROM `admin` WHERE `login` = ?;'
        );
        $stmt->execute([$this->login]);
        if($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($this->password, $result['password'])){
                $algo = 'AES-256-CTR';
                /**
                 * Generate iv & key for user's mail encryption
                 */
                $iv1 = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
                $key1 = openssl_random_pseudo_bytes(64);
                $crypted_login = rawurlencode(openssl_encrypt(
                    $this->login,
                    $algo,
                    $key1,
                    OPENSSL_ZERO_PADDING,
                    $iv1
                ));
                $iv2 = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
                $key2 = openssl_random_pseudo_bytes(64);
                $crypted_time = rawurlencode(openssl_encrypt(
                    time(),
                    $algo,
                    $key2,
                    OPENSSL_ZERO_PADDING,
                    $iv2
                ));
                $iv1 = base64_encode($iv1);
                $iv2 = base64_encode($iv2);
                $key1 = base64_encode($key1);
                $key2 = base64_encode($key2);

                $stmt = self::$db->prepare(
                    'UPDATE `admin` SET `iv` = ?, `key` = ?, `iv_time` = ?, `key_time` = ? WHERE `login` = ?;'
                );
                $stmt->execute([$iv1, $key1, $iv2, $key2, $this->login]);
                if($stmt->rowCount()){
                    $link = URL . 'admin?l=' . $crypted_login . '&a=1&t=' . $crypted_time . '&o=' . $this->login;
                    $table = 'admin';
                    require ROOT . 'mailer/mailer.php';
                    return 'success';
                }
                else return 'failure';
            }
            else return 'invalid_login';
        }
        else return 'invalid_login';
    }
    
    function confirmLogin(){
        $stmt = self::$db->prepare(
            "SELECT `iv`, `key`, `iv_time`, `key_time` FROM `admin` WHERE `login` = ?"
        );
        $stmt->execute([$this->o]);
        if($keychain = $stmt->fetch(PDO::FETCH_ASSOC)){
            $algo = 'AES-256-CTR';
            $login = openssl_decrypt(
                rawurldecode($_GET['l']),
                $algo,
                base64_decode($keychain['key']),
                OPENSSL_ZERO_PADDING,
                base64_decode($keychain['iv'])
            );
            $time = openssl_decrypt(
                rawurldecode($_GET['t']),
                $algo,
                base64_decode($keychain['key_time']),
                OPENSSL_ZERO_PADDING,
                base64_decode($keychain['iv_time'])
            );
            if($login === $this->o){
                if(time() - $time <= 300){
                    $stmt = self::$db->prepare(
                        "UPDATE `admin` SET `key` = NULL, `iv` = NULL, `iv_time` = NULL, `key_time` = NULL WHERE `login` = ?;"
                    );
                    $stmt->execute([$login]);
                    if($stmt->rowCount()){
                        $session = new Session();
                        $this->authtoken = $session->giveCookie('ADMauthtoken');
                        $stmt = self::$db->prepare(
                            'UPDATE `admin` 
                            SET `authtoken` = ? 
                            WHERE `login` = ?'
                        );
                        $stmt->execute([$this->authtoken, $login]);
                        return 'success';
                    }
                    else 
                        return 'failure';
                }
                else return 'invalid_time';
            }
            else return 'invalid_user';
        }
        else return 'invalid_user';
    }

    public function setNewPassword(){
        if(!empty($this->password) && !empty($_POST['cpassword'])){
            if($this->password === $this->cpassword){
                if(self::verifyPwd($this->password)){
                    $stmt = self::$db->prepare(
                        'UPDATE `admin` 
                        SET `password` = ? 
                        WHERE `authtoken` = ?'
                    );
                    $stmt->execute([password_hash($this->password, PASSWORD_DEFAULT), $this->authtoken]);
                    if($stmt->rowCount())
                        return 'modify_success';
                    else
                        return 'modify_failure';
                }
                else return 'invalid_strenght';
            }
            else return 'invalid_match';
        }
        else return 'invalid_input';
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