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
        $query = self::$db->query(
            'SELECT p.`id`, m.`id` AS `id_marque`, m.`nom` AS `nom_marque`, p.`nom` AS `nom_produit`, p.`description`, p.`prix`, p.`stock`, p.`active` 
            FROM `produits` p 
            INNER JOIN `marques` m 
            ON m.`id` = p.`id_marque` 
            ORDER BY m.`active` DESC, `nom_marque`, `id`;'
        );
        $content['produits'] = $query->fetchAll(PDO::FETCH_ASSOC);
        $query = self::$db->query(
            'SELECT `id`, `nom`, `description`, `active`  
            FROM `marques`
            ORDER BY `active` DESC, `nom` ASC;'
        );
        $content['marques'] = $query->fetchAll(PDO::FETCH_ASSOC);
        $query = self::$db->query(
            'SELECT DISTINCT `nom` FROM `promotions`;'
        );
        $content['promotions'] = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($content['promotions'] as $key => $promotion){
            $stmt = self::$db->prepare(
                'SELECT p.`id`, p.`id_produit`, pt.`nom` AS `nom_produit`, m.`nom` AS `nom_marque`, p.`pourcentage`, p.`debut`, p.`fin` 
                FROM `promotions` p 
                INNER JOIN `produits` pt ON p.`id_produit` = pt.`id` 
                INNER JOIN `marques` m ON m.`id` = pt.`id_marque` 
                WHERE p.`nom` = ?
                ORDER BY p.`debut`, p.`nom`;'
            );
            $stmt->execute([$promotion['nom']]);
            $content['promotions'][$promotion['nom']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            unset($content['promotions'][$key]);
        }
        return $content;
    }

    public function fetchAdmins(){
        $stmt = self::$db->query(
            'SELECT `login` FROM `admins`;'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}