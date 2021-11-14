<?php

if(!empty($_POST['delete']) && in_array($_POST['delete'], ['address'])){
    $jshandler = new JSHandler($_POST);
    $jshandler->delete();
}
else if(!empty($_POST['adm_modify']) && in_array($_POST['adm_modify'], ['clients'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMmodify();
    }
    else echo 'unauthorized';
}

class JSHandler{
    protected static $db;
    
    public function __construct(array $array){
        self::$db = new PDO('mysql:host=localhost;dbname=shop', 'root', '');
        foreach($array as $key => $value)
            $this->$key = htmlentities($value, ENT_QUOTES, "UTF-8");
    }

    public function delete(){
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

    public function authAdmin(){
        $stmt = self::$db->prepare(
            "SELECT `id` FROM `admin` WHERE `authtoken` = ?;"
        );
        $stmt->execute([$this->authtoken]);
        if($stmt->fetch(PDO::FETCH_ASSOC)) return 1;
        else return 0;
    }

    public function ADMmodify(){
        switch($this->adm_modify){
            case 'clients':
                $stmt = self::$db->prepare(
                    "UPDATE `clients` SET $this->item = ? 
                    WHERE `id` = ?;"
                );
                $stmt->execute([$this->value, $this->id_client]);
                if($stmt->rowCount()) echo "success";
                else echo "failure";
                break;
            default:
                echo "error";
                break;
        }
    }
}