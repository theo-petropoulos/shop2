<main id="admin_alt">
    <section id="admins_list">
        <?php foreach($admins as $key => $value){?>
            <div class="admin_resume">
                <button class="del_admin_btn">X</button>
                <h3>Identifiant</h3>
                <p><?=$value['login'];?></p>
            </div>
        <?php } ?>
    </section>

    <section id="add_admins_form_container">
        <div id="add_admins" class="add_container">
            <button id="add_admin_btn">Ajouter un administrateur</button>
            <div id="add_admins_form_container" class="add_form_container">
                <form method="post" action="" id="add_admins_form" class="add_form">
                    <button class="close_form_btn">X</button>
                    <h3>Nouvel administrateur</h3>
                    <label for="login">Identifiant :</label>
                    <input type="text" name="login" value="Admin_2" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" name="password" value="Test123!" minlength="8" required>
                    <label for="cpassword">Confirmez le mot de passe :</label>
                    <input type="password" name="cpassword" value="Test123!" minlength="8" required>
                    <input type="submit" name="Ajouter">
                </form>
            </div>
        </div>
    </section>

    <section id="admins_response">
    </section>

    <a href="admin" id="back_btn">Retour</a>
</main>