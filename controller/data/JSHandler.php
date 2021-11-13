<?php

if(!empty($_POST['delete']) && in_array($_POST['delete'], ['address'])){
    $jshandler = new JSHandler($_POST);
}

class JSHandler{
    protected static $db;
    
    public function __construct(array $array){
        self::$db = new PDO('mysql:host=localhost;dbname=shop', 'root', '');
        foreach($array as $key => $value)
            $this->$key = htmlentities($value, ENT_QUOTES, "UTF-8");

        switch($this->delete){
            case 'address':
                $stmt = self::$db->prepare(
                    'DELETE FROM `adresses` a 
                    WHERE a.`id` = ? AND a.`id_client` = 
                        ( SELECT `id` FROM `clients` c 
                        WHERE c.`authtoken` = ? );'
                );
                $stmt->execute([$this->id, $this->authtoken]);
                if($stmt->rowCount()){
                    echo "success";
                }
                else echo "no_effect";
                break;
            default:
                echo "error";
                break;
        }
    }
}