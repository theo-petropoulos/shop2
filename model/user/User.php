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
        foreach($this as $item => $value){
            echo $item . '<br>';
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
                var_dump(preg_match("/^[a-zA-Z'-âàéèêôîûÂÀÉÈÊ ]*$/", $value));
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
        $stmt = self::$db->prepare(
            'SELECT `id` FROM `clients` 
            WHERE `mail` = ? OR `telephone` = ?'
        );
        $stmt->execute([$this->mail, $this->phone]);
        if(!empty($stmt->fetch())){
            return 'user_exists';
        }
        else{

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