<?php

class Profile extends Database{

    public function __construct(array $array = NULL){
        parent::__construct();
        $this->ip = $_SERVER['REMOTE_ADDR'];
        if(!empty($array)){
            foreach($array as $item => $value)
                $this->$item = htmlspecialchars($value, ENT_QUOTES);
        }
        $infos = self::fetchInfos($_COOKIE['authtoken']);
        foreach($infos as $info => $value){
            $this->$info = htmlspecialchars($value, ENT_QUOTES);
        }
        return $this;
    }

    public function getHis(string $item){
        return $this->$item ?? null;
    }

    public function fetchAddresses(){
        $stmt = self::$db->prepare(
            'SELECT a.id, a.nom, a.prenom, a.numero, a.rue, a.complement, a.code_postal, a.ville 
            FROM adresses a 
            WHERE a.id_client = ( SELECT c.id FROM clients c WHERE c.authtoken = ? );'
        );
        $stmt->execute([$this->authtoken]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addNewAddress(array $array){
        foreach($array as $item => $value)
            $this->$item = htmlspecialchars($value, ENT_QUOTES);
        if(!empty($this->nom) && !empty($this->prenom) && !empty($this->rue) && !empty($this->ville) && !empty($this->code_postal)){
            $stmt = self::$db->prepare(
                'INSERT INTO `adresses` (id_client, nom, prenom, numero, rue, code_postal, ville) 
                VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$this->id_client, $this->nom, $this->prenom, $this->numero, $this->rue, $this->code_postal, $this->ville]);
            if($stmt->rowCount()){
                return 1;
            }
            else return 0;
        }
        else return 0;
    }

    public function delete(){
        $stmt = self::$db->prepare(
            'UPDATE `clients` 
            SET `active` = NULL
            WHERE `authtoken` = ?;'
        );
        $stmt->execute([$this->authtoken]);
        if($stmt->rowCount()){
            setcookie(
                'authtoken',
                '',
                -1,
                '/shop/'
            );
            return 'success';
        }
        else return 'failure';
    }

    private static function fetchInfos(string $authtoken = NULL){
        $stmt = self::$db->prepare(
            'SELECT c.`id` AS `id_client`, c.`nom`, c.`prenom`, c.`mail`, c.`telephone` 
            FROM `clients` c 
            WHERE c.`authtoken` = ?'
        );
        $stmt->execute([$authtoken]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}