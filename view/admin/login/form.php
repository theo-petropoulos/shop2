<main id="admin">
    <?php if(empty($success)){ ?>
    <form method="post" action="">
        <input type="hidden" name="admin_login" value="1">
        <label for="login">Identifiant :</label>
        <input type="text" name="login" value="Admin" required>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" value="Test123!" required>
        <input type="submit" value="Connexion">
    </form>
    <?php } else echo '<p>' . $success['message'] . '</p>'; ?>
</main>