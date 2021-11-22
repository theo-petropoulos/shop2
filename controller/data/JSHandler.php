<?php
if(!empty($_POST['delete']) && in_array($_POST['delete'], ['address'])){
    $jshandler = new JSHandler($_POST);
    $jshandler->delete();
}
elseif(!empty($_POST['adm_modify']) && in_array($_POST['adm_modify'], ['clients', 'marques', 'produits', 'promotions'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMmodify();
    }
    else echo 'unauthorized';
}
elseif(!empty($_POST['adm_create']) && in_array($_POST['adm_create'], ['produits', 'marques', 'promotions']) ){
    $jshandler = new JSHandler($_POST, $_FILES);
    if($jshandler->authAdmin()){
        $jshandler->ADMcreate();
    }
    else echo 'unauthorized';
}
elseif(!empty($_POST['adm_delete']) && in_array($_POST['adm_delete'], ['produits', 'marques', 'promotions'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMdelete();
    }
}
elseif(!empty($_POST['adm_search'])){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMsearch();
    }
}
elseif(!empty($_POST['adm_create_adm']) && $_POST['adm_create_adm'] == 1){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMcreateADM();
    }
}
elseif(!empty($_POST['adm_delete_adm']) && $_POST['adm_delete_adm'] == 1){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMdeleteADM();
    }
}
elseif(!empty($_POST['adm_delete_discount']) && $_POST['adm_delete_discount'] == 1){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMdeleteDiscount();
    }
}
elseif(!empty($_POST['adm_fetch_products']) && $_POST['adm_fetch_products'] == 1){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMfetchProducts();
    }
}
elseif(!empty($_POST['adm_fetch_marques']) && $_POST['adm_fetch_marques'] == 1){
    $jshandler = new JSHandler($_POST);
    if($jshandler->authAdmin()){
        $jshandler->ADMfetchBrands();
    }
}

class JSHandler{
    protected static $db;
    
    public function __construct(array $array, array $file = NULL){
        self::$db = new PDO('mysql:host=localhost;dbname=shop', 'root', '');
        foreach($array as $key => $value){
            if($key === 'id' && intval($value) != $value) $this->nom = $value;
            else $this->$key = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
        }
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
            "SELECT `id` FROM `admins` WHERE `authtoken` = ?;"
        );
        $stmt->execute([$this->authtoken]);
        if($stmt->fetch(PDO::FETCH_ASSOC)) return 1;
        else return 0;
    }

    public function ADMmodify(){
        if($this->adm_modify !== 'promotions'){
            $stmt = self::$db->prepare(
                "UPDATE $this->adm_modify SET $this->item = ? 
                WHERE `id` = ?;"
            );
            $stmt->execute([$this->value, $this->id]);
        }
        elseif($this->adm_modify === 'promotions'){
            if(empty($this->nom)){
                $stmt = self::$db->prepare(
                    'SELECT `nom` FROM `promotions` WHERE `id` = ?;'
                );
                $stmt->execute([$this->id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->nom = $result['nom'];
            }
            $stmt = self::$db->prepare(
                "UPDATE $this->adm_modify SET $this->item = ? 
                WHERE `nom` = ?;"
            );
            $stmt->execute([$this->value, $this->nom]);
        }
        if($stmt->rowCount()) echo "success";
        else echo "failure";
    }

    public function ADMcreate(){
        if($this->adm_create !== 'promotions'){
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
                if($stmt->rowCount()){
                    echo "SUCCESS";
                }
                else echo "ERR_SQL_INSRT";
            }
        }
        elseif($this->adm_create === 'promotions'){
            if(intval($this->id_produit)){
                $stmt = self::$db->prepare(
                    'BEGIN;
                    DELETE FROM `promotions` WHERE `id_produit` = ?;
                    INSERT INTO `promotions` (`id_produit`, `nom`, `pourcentage`, `debut`, `fin`)
                    VALUES ( ?, ?, ?, ?, ?);
                    COMMIT;'
                );
                $stmt->execute([$this->id_produit, $this->id_produit, $this->nom, $this->pourcentage, $this->debut, $this->fin]);
                if($stmt->rowCount()){
                    echo 'SUCCESS';
                }
                else echo 'ERR_SQL_INSRT';
            }
            else{
                if(intval($this->id_marque)){
                    $stmt = self::$db->prepare(
                        'SELECT `id` FROM `produits`
                        WHERE `id_marque` = ?;'
                    );
                    $stmt->execute([$this->id_marque]);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($products as $key => $product){
                        $stmt = self::$db->prepare(
                            'BEGIN;
                            DELETE FROM `promotions` WHERE `id_produit` = ?;
                            INSERT INTO `promotions` (`id_produit`, `nom`, `pourcentage`, `debut`, `fin`)
                            VALUES ( ?, ?, ?, ?, ?);
                            COMMIT;'
                        );
                        $stmt->execute([$product['id'], $product['id'], $this->nom, $this->pourcentage, $this->debut, $this->fin]);
                        if($stmt->rowCount()){
                            echo 'SUCCESS';
                        }
                        else echo 'ERR_SQL_INSRT';
                    }
                }
                else if($this->id_marque === 'all_marques' && $this->id_produit === 'all_produits'){
                    $stmt = self::$db->query(
                        'SELECT p.`id` 
                        FROM `produits` p'
                    );
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($products as $key => $product){
                        $stmt = self::$db->prepare(
                            'INSERT INTO `promotions` (`id_produit`, `nom`, `pourcentage`, `debut`, `fin`)
                            VALUES ( ?, ?, ?, ?, ?);'
                        );
                        $stmt->execute([$product['id'], $this->nom, $this->pourcentage, $this->debut, $this->fin]);
                        if($stmt->rowCount()){
                            echo 'SUCCESS';
                        }
                        else echo 'ERR_SQL_INSRT';
                    }
                }
            }
        }
    }

    public function ADMdelete(){
        if($this->adm_delete === 'produits' || $this->adm_delete === 'promotions'){
            if($this->adm_delete === 'produits'){
                $stmt = self::$db->prepare(
                    "SELECT `image_path` 
                    FROM $this->adm_delete 
                    WHERE `id` = ?;"
                );
                $stmt->execute([$this->id]);
                if(!empty($result = $stmt->fetch(PDO::FETCH_ASSOC))){
                    $this->path = $_SERVER['DOCUMENT_ROOT'] . '/shop/' . $result['image_path'];
                    is_file($this->path) ? unlink($this->path) : null;
                }
            }
            $stmt = self::$db->prepare(
                "DELETE FROM $this->adm_delete 
                WHERE `id` = ?;"
            );
            $stmt->execute([$this->id]);
            if($stmt->rowCount())
                echo "SUCCESS";
            else 
                echo "ERR_SQL_INSRT";
        }
        elseif($this->adm_delete === 'marques'){
            $stmt = self::$db->prepare(
                "SELECT `image_path`
                FROM `produits` 
                WHERE `id_marque` = ?;"
            );
            $stmt->execute([$this->id]);
            if(!empty($results = $stmt->fetchAll(PDO::FETCH_ASSOC))){
                foreach($results as $key => $result){
                    $this->path = $_SERVER['DOCUMENT_ROOT'] . '/shop/' . $result['image_path'];
                    is_file($this->path) ? unlink($this->path) : null;
                }
            }
            $stmt = self::$db->prepare(
                "BEGIN;
                DELETE FROM `produits` 
                WHERE `id_marque` = ?;
                DELETE FROM `marques` 
                WHERE `id` = ?;
                COMMIT;"
            );
            $stmt->execute([$this->id, $this->id]);
            if($stmt->rowCount())
                echo "SUCCESS";
            else 
                echo "ERR_SQL_INSRT";
        }
    }

    public function ADMsearch(){
        $search = "% $this->adm_search%";
        $search2 = "$this->adm_search%";
        switch($this->table){
            case 'produits':
                $stmt = self::$db->prepare(
                    "SELECT DISTINCT p.`id`, m.`nom` AS `nom_marque`, p.`nom` AS `nom_produit`, p.`description`, p.`prix`, p.`stock`, p.`active` 
                    FROM `produits` p 
                    INNER JOIN `marques` m 
                    ON m.`id` = p.`id_marque` 
                    WHERE ( m.`nom` LIKE ? ) OR ( m.`nom` LIKE ? ) OR ( p.`nom` LIKE ? ) OR ( p.`nom` LIKE ? )
                    ORDER BY `nom_produit`, `nom_marque`;"
                );
                $stmt->execute([$search2, $search, $search2, $search]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
                break;
            case 'marques':
                $stmt = self::$db->prepare(
                    "SELECT `id`, `nom`, `description`, `active`  
                    FROM `marques` WHERE `nom` LIKE ? OR `nom` LIKE ?
                    ORDER BY `nom` ASC;"
                );
                $stmt->execute([$search, $search2]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
                break;
            case 'promotions':
                $stmt = self::$db->prepare(
                    'SELECT p.`id`, p.`id_produit`, p.`nom` AS `nom_promotions`, pt.`nom` AS `nom_produit`, m.`nom` AS `nom_marque`, p.`pourcentage`, p.`debut`, p.`fin` 
                    FROM `promotions` p 
                    INNER JOIN `produits` pt ON p.`id_produit` = pt.`id` 
                    INNER JOIN `marques` m ON m.`id` = pt.`id_marque` 
                    WHERE p.`nom` LIKE ? OR p.`nom` LIKE ? OR pt.`nom` LIKE ? OR pt.`nom` LIKE ? 
                    ORDER BY p.`nom`, pt.`nom`;'
                );
                $stmt->execute([$search, $search2, $search, $search2]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($results);
                break;
            default:
                echo "ERR_SEARCH";
                break;
        }
    }

    public function ADMcreateADM(){
        if($this->password === $this->cpassword){
           if( !preg_match('@[A-Z]@', $this->password) || !preg_match('@[a-z]@', $this->password) ||
			!preg_match('@[0-9]@', $this->password) || !preg_match('@[^\w]@', $this->password) ||
			strlen($this->password)<8 ){
                echo "ERR_PWD_STRG";
				return 0;
			}
            else{
                $stmt = self::$db->prepare(
                    "SELECT `login` FROM `admins` WHERE `login` = ?;"
                );
                $stmt->execute([$this->login]);
                if(!empty($stmt->fetch(PDO::FETCH_ASSOC))){
                    echo "ERR_LOG_EXST";
                    return 0;
                }
                else{
                    $stmt = self::$db->prepare(
                        'INSERT INTO `admins` ( `login`, `password` ) 
                        VALUES ( ?, ? );'
                    );
                    $stmt->execute([$this->login, password_hash($this->password, PASSWORD_DEFAULT)]);
                }
                if($stmt->rowCount()){
                    echo "SUCCESS";
                    return 0;
                }
                else{
                    echo "ERR_SQL_INSR";
                    return 0;
                }
            }
        }
        else{
            echo "ERR_PWD_MATCH";
            return 0;
        }
    }

    public function ADMdeleteADM(){
        $stmt = self::$db->prepare(
            'SELECT `login` FROM `admins` WHERE `authtoken` = ?;'
        );
        $stmt->execute([$this->authtoken]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['login'] === $this->login){
            echo "ERR_IS_ADM";
            return 0;
        }
        else{
            $stmt = self::$db->prepare(
                'DELETE FROM `admins` WHERE `login` = ?;'
            );
            $stmt->execute([$this->login]);
            if($stmt->rowCount()){
                echo "SUCCESS";
                return 0;
            }
            else{
                echo "ERR_SQL_DEL";
                return 0;
            }
        }
    }

    public function ADMdeleteDiscount(){
        $stmt = self::$db->prepare(
            'DELETE FROM `promotions` 
            WHERE `nom` = ?;'
        );
        $stmt->execute([$this->nom]);
        if($stmt->rowCount())
            echo "SUCCESS";
        else 
            echo "ERR_SQL_DEL";
    }

    public function ADMfetchProducts(){
        $stmt = self::$db->prepare(
            'SELECT p.`id`, p.`nom`, p.`prix` 
            FROM `produits` p 
            WHERE p.`id_marque` = ? 
            ORDER BY p.`nom`;'
        );
        $stmt->execute([$this->id_marque]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function ADMfetchBrands(){
        $query = self::$db->query(
            'SELECT m.`id`, m.`nom` 
            FROM `marques` m 
            ORDER BY m.`nom`;'
        );
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    }
}