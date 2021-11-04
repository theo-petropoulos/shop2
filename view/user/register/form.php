<h3>Inscription</h3>

<form method="post" action="inscription">
    <label for="mail">Adresse mail :</label>
    <input type="email" name="mail" minlength=6 maxlength=50 required>
    <label for="password">Mot de passe :</label>
    <input type="password" name="password" value="Test123!" minlength=8 maxlength=50 required>
    <label for="cpassword">Confirmez le mot de passe :</label>
    <input type="password" name="cpassword" value="Test123!" minlength=8 maxlength=50 required>
    <label for="lastname">Nom :</label>
    <input type="text" name="lastname" maxlength=30 required>
    <label for="firstname">Prénom :</label>
    <input type="text" name="firstname" maxlength=30 required>
    <label for="phone">Téléphone :</label>
    <input type="tel" name="phone" required>
    <input type="submit" value="Valider">
</form>