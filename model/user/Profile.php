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
        var_dump($this);
    }

    public function getHis(string $item){
        return $this->$item ?? null;
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