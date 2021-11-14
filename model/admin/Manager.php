<?php

class Manager extends Database{

    public function __construct(){
        parent::__construct();
    }

    public function fetchClients(){
        $stmt = self::$db->query(
            'SELECT `id`, `nom`, `prenom`, `mail`, `telephone`, `active` FROM `clients`;'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}