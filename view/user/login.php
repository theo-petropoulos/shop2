<main id="connexion">
    <div id="connexion_header">
        <h3>CONNEXION</h3>
        <h4>Vous n'êtes pas encore inscrit ? <a href="inscription">INSCRIPTION</a></h4>
    </div>

    <form method="post" action="profil">
        <input type="hidden" name="login" value=1>
        <label for="mail">Adresse mail :</label>
        <input type="email" name="mail" minlength=6 maxlength=50 required>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" minlength=8 maxlength=50 value="Test123!" required>
        <input type="submit" value="Connexion">
    </form>
</main>