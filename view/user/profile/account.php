<main id="account">
    <?php if(empty($success)){ ?>
        <h2>Suppression du compte</h2>
        <p>Vous êtes sur le point de supprimer votre compte. Toutes les commandes en cours continueront d'être honorées mais vous ne pourrez plus 
            accéder à votre espace personnel ni consulter votre historique d'achat.<br>Êtes-vous sûr de vouloir continuer ?
        </p>
        <span>
            <a href="profil?delete=1&confirm=1">Oui</a>
            <a href="profil">Non</a>
        </span>
    <?php } else if(!empty($success) && $success == 1){ ?>
        <h2>Navrés de vous voir partir</h2>
        <p>Votre compte a bien été supprimé. A des fins techniques et légales, les données de votre compte restent stockées dans notre base de données 
            afin de, par exemple, retrouver une commande ou encore réactiver votre compte à votre initiative. Pour vous opposer à cet effet, veuillez 
            contacter le délégué à la protection des données à l'adresse <a href="mailto:dpo@minimal-shop.com">dpo@minimal-shop.com</a>.<br>
            Bonne continuation et à bientôt peut-être !
        </p>
    <?php } ?>
</main>