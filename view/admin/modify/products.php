<main id="admin_alt">
    <div id="produits">
        <details id="marques_det">
            <summary>Afficher les marques</summary>
            <?php if(!empty($content) && !empty($content['marques'])) : foreach($content['marques'] as $key => $marque){?>
                <div id="marque_<?=$marque['id'];?>" class="div_det">
                    <?php foreach($marque as $key => $value){
                        if($key !== 'id'){ ?>
                            <div id="<?=$marque['id'] . '_' . $key . '_marque';?>" class="<?=$key;?>">
                                <p><?=$value != '' ? $value : '0'?></p>
                                <button class="adm_modify_button">Modifier</button>
                            </div>
                        <?php } 
                    } ?>
                    <a href="admin?modify=marques&show_products=1">Afficher les produits</a>
                </div>
            <?php } endif; ?>
        </details>

        <details id="produits_det">
            <summary>Afficher les produits</summary>
            <?php if(!empty($content) && !empty($content['produits'])) : foreach($content['produits'] as $key => $produit){?>
                <div id="produit_<?=$produit['id'];?>" class="div_det">
                    <?php foreach($produit as $key => $value){
                        if($key !== 'id'){ ?>
                            <div id="<?=$produit['id'] . '_' . $key . '_produit';?>" class="<?=$key;?>">
                                <h4><?=ucfirst($key);?></h4>
                                <p><?=$value != '' ? $value : '0'?></p>
                                <button class="adm_modify_button">Modifier</button>
                            </div>
                        <?php } 
                    } ?>
                </div>
            <?php } endif; ?>
        </details>
    </div>

    <section id="add_produits_marques">
        <div id="add_marques" class="add_container">
            <button id="add_marques_btn" class="add_btn">+ Ajouter une marque</button>
            <form enctype="multipart/form-data" method="post" action="" id="add_marques_form" class="add_form">
                <button class="close_form_btn">X</button>
                <h3>Nouvelle marque</h3>
                <label for="nom">Nom :</label>
                <input type="text" name="nom" required>
                <label for="description">Description :</label>
                <textarea id="description_area" name="description" required></textarea>
                <input type="submit" name="Ajouter">
            </form>
        </div>
        <div id="add_produits" class="add_container">
            <button id="add_produits_btn" class="add_btn">+ Ajouter un produit</button>
            <form enctype="multipart/form-data" method="post" action="" id="add_produits_form" class="add_form">
                <button class="close_form_btn">X</button>
                <h3>Nouveau produit</h3>
                <input type="file" name="image" id="image_form" accept="image/*" required>
                <label for="marque">Marque:</label>
                <select name="id_marque">
                    <?php foreach($content['marques'] as $key => $marque){ ?>
                        <option value="<?=$marque['id'];?>"><?=$marque['nom'];?></option>
                    <?php } ?>
                </select>
                <label for="nom">Nom :</label>
                <input type="text" name="nom" required>
                <label for="description">Description :</label>
                <textarea id="description_area" name="description" required></textarea>
                <label for="prix">Prix :</label>
                <input type="number" step="0.01" name="prix" required>
                <label for="stock">Stock :</label>
                <input type="number" name="stock" required>
                <span>
                    <label for="active">Activer ?</label>
                    <input type="checkbox" name="active">
                </span>
                <input type="submit" name="Ajouter">
            </form>
        </div>
    </section>

    <a href="admin" id="back_btn">Retour</a>
</main>