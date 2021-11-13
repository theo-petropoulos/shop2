<main id="addresses">
    <h2>Carnet d'adresses</h2>
    <section id="registered_addresses">
        <?php if(!empty($addresses)){
            foreach($addresses as $key => $address){ ?>
                <div id="address_<?=$address['id'];?>">
                    <button class="delete_address">X</button>
                    <p><?=$address['nom'];?></p>
                    <p><?=$address['prenom'];?></p>
                    <p><?=$address['numero'] . " " . $address['rue'];?></p>
                    <p><?=str_pad($address['code_postal'], 5, '0', STR_PAD_LEFT);?></p>
                    <p><?=$address['ville'];?></p>
                </div>
            <?php } ?>
        <?php } ?>
    </section>
    <form method="post" action="profil?modify=address">
        <h2>Ajouter une nouvelle adresse</h2>
        <input type="hidden" name="modify_address" value="1">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" placeholder="Dupont" required>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" placeholder="Jean" required>
        <div id="address_num_holder">
            <label for="rue">Adresse :</label>
            <span>
                <input type="number" name="numero" placeholder="Numéro">
                <input type="text" name="rue" placeholder="Avenue des coquelicots" required>
            </span>
        </div>
        <label for="code_postal">Code postal :</label>
        <input type="number" name="code_postal" pattern="[0-9]{3}" placeholder="75015" required>
        <label for="ville">Ville :</label>
        <input type="text" name="ville" placeholder="Paris" required>
        <input type="submit" value="Ajouter">
    </form>
    <section id="return">
        <?php if(!empty($success) && $success == 1) echo "<p>Adresse ajoutée avec succès.</p>";?>
    </section>
    <a href="profil">Retour</a>
</main>