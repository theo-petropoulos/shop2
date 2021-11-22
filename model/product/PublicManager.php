<?php

class PublicManager extends Database{

    public function __construct(){
        parent::__construct();
    }

    public function fetchProducts(){
        $query = self::$db->query(
            'SELECT `id`, `nom`, `description`, `active`  
            FROM `marques`
            ORDER BY `active` DESC, `nom` ASC;'
        );
        $content['marques'] = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($content['marques'] as $key => $marque){
            $stmt = self::$db->prepare(
                'SELECT m.`id` AS `id_marque`, p.`id`, p.`nom`, p.`description`, p.`stock`, p.`prix`, p.`image_path`,
                    (   
                        CASE WHEN EXISTS(SELECT pr.`pourcentage` FROM `promotions` pr WHERE pr.`id_produit` = p.`id` AND NOW() >= pr.`debut` && NOW() <= pr.`fin` )
                        THEN (SELECT pr.`pourcentage` FROM `promotions` pr WHERE pr.`id_produit` = p.`id`)
                        ELSE 0
                        END 
                    ) AS `pourcentage`
                FROM `produits` p 
                INNER JOIN `marques` m ON m.`id` = p.`id_marque` 
                WHERE m.`id` = ?
                ORDER BY p.`nom`;'
            );
            $stmt->execute([$marque['id']]);
            $content['marques'][$marque['nom']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            unset($content['marques'][$key]);
        }
        return $content;
    }
}