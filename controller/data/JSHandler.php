<?php
if(!empty($_POST['delete']) && in_array($_POST['delete'], ['address'])){
    $jshandler = new JSHandler($_POST);
    $jshandler->delete();
}
elseif(!empty($_POST['adm_modify']) && in_array($_POST['adm_modify'], ['clients', 'marques', 'produits'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMmodify();
    }
    else echo 'unauthorized';
}
elseif(!empty($_POST['adm_create']) && in_array($_POST['adm_create'], ['produits', 'marques']) ){
    $jshandler = new JSHandler($_POST, $_FILES);
    if($jshandler->authAdmin()){
        $jshandler->ADMcreate();
    }
    else echo 'unauthorized';
}
elseif(!empty($_POST['adm_delete']) && in_array($_POST['adm_delete'], ['produits', 'marques'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMdelete();
    }
}

class JSHandler{
    protected static $db;
    
    public function __construct(array $array, array $file = NULL){
        self::$db = new PDO('mysql:host=localhost;dbname=shop', 'root', '');
        foreach($array as $key => $value)
                $this->$key = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
        if(!empty($file))
            foreach($file as $key => $value) $this->$key = $value;
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
        $stmt = self::$db->prepare(
            "UPDATE $this->adm_modify SET $this->item = ? 
            WHERE `id` = ?;"
        );
        $stmt->execute([$this->value, $this->id]);
        if($stmt->rowCount()) echo "success";
        else echo "failure";
    }

    public function ADMcreate(){
        $values = [];
        $col_string = '';
        $param_string = '';
        $i = 0;
        if(!empty($this->image)){
            $j = 0;
            if($this->image['size'] < 10000000 && $this->image['error'] === UPLOAD_ERR_OK){
                $temp_path = $this->image['tmp_name'];
                $picture_name = $this->image['name'];
                $picture_name_split = explode(".", $picture_name);
                $extension = strtolower(end($picture_name_split));
                if(in_array($extension, ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'svg'])){
                    $picture_hash_name = md5(time() . $this->image['name']) . '.' . $extension;
                    $path = '../../assets/uploads/' . $this->adm_create . '/' . $picture_hash_name;
                    $dbpath = 'assets/uploads/' . $this->adm_create . '/' . $picture_hash_name;
                    if(move_uploaded_file($temp_path, $path)){
                        $j = 1;
                    }
                }
            }
        }
        if((isset($j) && $j) || !isset($j)){
            foreach($this as $key => $value){
                if(!in_array($key, ['authtoken', 'adm_create', 'image'])){
                    if($key === 'active'){
                        $i = 1;
                    }
                    else{
                        array_push($values, $value);
                        if($col_string === '') $col_string = '`' . $key . '`';
                        else $col_string .= ', `' . $key . '`';
                        $param_string = $param_string === '' ? '?' : $param_string . ', ?';
                    }
                }
            }
            $col_string .= ', `active`';
            array_push($values, $i);
            $param_string .= ', ?';
            if(!empty($this->image)){
                $col_string .= ', `image_path`';
                $param_string .= ', ?';
                array_push($values, $dbpath);
            }
            $query = 'INSERT INTO ' . $this->adm_create . '(' . $col_string . ') VALUES ( ' . $param_string . ')';
            $stmt = self::$db->prepare($query);
            $stmt->execute($values);
        }
    }

    public function ADMdelete(){
        if($this->adm_delete === 'produits'){
            $stmt = self::$db->prepare(
                "DELETE FROM $this->adm_delete 
                WHERE `id` = ?;"
            );
            $stmt->execute([$this->id]);
        }
        elseif($this->adm_delete === 'marques'){
            $stmt = self::$db->prepare(
                "BEGIN;
                DELETE FROM `produits` 
                WHERE `id_marque` = ?;
                DELETE FROM `marques` 
                WHERE `id` = ?;
                COMMIT;"
            );
            $stmt->execute([$this->id, $this->id]);
        }
    }
}