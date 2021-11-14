<main id="admin">
    <section id="password">
        <h2>Modification du mot de passe</h2>
        <form method="post" action="admin">
            <input type="hidden" name="modify_password" value=1>
            <label for="password">Nouveau mot de passe :</label>
            <input type="password" name="password" minlength="8" required>
            <label for="cpassword">Confirmez le mot de passe :</label>
            <input type="password" name="cpassword" minlength="8" required>
            <input type="submit" value="Valider">
        </form>
        <div id="return">
            <?php if(!empty($success) && $success === 1) echo '<p>Mot de passe modifié avec succès.</p>';?>
        </div>
        <a href="admin">Retour</a>
    </section>
</main>