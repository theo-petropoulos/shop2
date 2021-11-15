<main id="profile">
    <section id="landing">
        <div id="landing_titles">
            <h2>Bonjour <?=html_entity_decode($profile->prenom, ENT_QUOTES, 'UTF-8');?></h2>
            <a href="profil?disconnect=1">Se déconnecter</a>
        </div>
        <div id="landing_content">
            <a href="profil?modify=password">Modifier mon mot de passe</a>
            <a href="profil?modify=addresses">Mes adresses</a>
            <a href="profil?modify=orders">Mes commandes</a>
            <a href="profil?delete=1">Supprimer mon compte</a>
        </div>
    </section>
</main>