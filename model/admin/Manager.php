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

    public function fetchProducts(){
        $content = [];
        $stmt = self::$db->query(
            'SELECT p.`id`, m.`nom` AS `nom_marque`, p.`nom` AS `nom_produit`, p.`description`, p.`prix`, p.`stock`, p.`active` 
            FROM `produits` p 
            INNER JOIN `marques` m 
            ON m.`id` = p.`id_marque` 
            ORDER BY m.`active` DESC, `nom_marque`, `id`;'
        );
        $content['produits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = self::$db->query(
            'SELECT `id`, `nom`, `description`, `active`  
            FROM `marques`
            ORDER BY `active` DESC, `nom` ASC;'
        );
        $content['marques'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $content;
    }

    public function fetchAdmins(){
        $stmt = self::$db->query(
            'SELECT `login` FROM `admins`;'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}