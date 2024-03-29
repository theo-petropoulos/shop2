<main id="admin_alt">
    <div id="adm_search_container">
        <section id="adm_search_marques">
            <button type="submit" class="trigger_adm_search">Rechercher une marque</button>
        </section>
        <section id="adm_search_produits">
            <button type="submit" class="trigger_adm_search">Rechercher un produit</button>
        </section>
        <section id="adm_search_promotions">
            <button type="submit" class="trigger_adm_search">Rechercher une promotion</button>
        </section>
    </div>

    <div id="produits">
        <details id="promotions_det">
            <summary>Afficher les promotions</summary>
            <?php if(!empty($content) && !empty($content['promotions'])) : foreach($content['promotions'] as $key => $promotion){ ?>
                <details id="promotions_<?=$key?>" class="det_det">
                    <summary>
                        <div class="span_div">
                            <i class="fas fa-caret-right"></i>
                            <i class="fas fa-caret-down"></i>
                        </div>
                        <div class="span_div" id="<?=$key;?>_nom">
                            <h3>Promotion :</h3>
                            <p><?=$key;?></p>
                            <button class="adm_modify_button">Modifier</button>
                        </div>
                        <div class="span_div" id="<?=$key;?>_pourcentage">
                            <h3>Pourcentage :</h3>
                            <p><?=$promotion[0]['pourcentage'];?></p>
                            <button class="adm_modify_button">Modifier</button>
                        </div>
                        <div class="span_div" id="<?=$key;?>_debut">
                            <h3>Début :</h3>
                            <p><?=$promotion[0]['debut'];?></p>
                            <button class="adm_modify_button">Modifier</button>
                        </div>
                        <div class="span_div" id="<?=$key;?>_fin">
                            <h3>Fin :</h3>
                            <p><?=$promotion[0]['fin'];?></p>
                            <button class="adm_modify_button">Modifier</button>
                        </div>
                        <div class="span_div">
                            <a href="admin_delete_promotions" id="<?=$key;?>">Supprimer la promotion</a>
                        </div>
                    </summary>
                    <div>
                        <?php foreach($promotion as $key => $promo_item){ ?>
                            <div id="promotions_<?=$promo_item['id'];?>" class="div_det">
                                <button class="adm_delete_btn">X</button>
                                <?php foreach($promo_item as $key => $value){
                                    if($key === 'nom_marque'){?>
                                        <div id="produit_<?=$promo_item['id_produit'];?>" class="<?=$key;?>">
                                            <h3>Produit :</h3>
                                            <p><?=$value != '' ? $value : '0'?> - <?=$promo_item['nom_produit'];?></p>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        <?php } ?>
                    </div>
                </details>
                <?php } endif;?>
        </details>
        <details id="marques_det">
            <summary>Afficher les marques</summary>
            <?php if(!empty($content) && !empty($content['marques'])) : foreach($content['marques'] as $key => $marque){?>
                <div id="marques_<?=$marque['id'];?>" class="div_det">
                    <button class="adm_delete_btn">X</button>
                    <?php foreach($marque as $key => $value){
                        if(!in_array($key, ['id_marque', 'id'])){ ?>
                            <div id="<?=$marque['id'] . '_' . $key . '_marques';?>" class="<?=$key;?>">
                                <h3><?=ucfirst($key);?></h3>
                                <p><?=$value != '' ? $value : '0'?></p>
                                <button class="adm_modify_button">Modifier</button>
                            </div>
                        <?php } 
                    } ?>
                    <a href="admin_marques_show_products" id="<?=$marque['nom'];?>_link">Afficher les produits</a>
                </div>
            <?php } endif; ?>
        </details>

        <details id="produits_det">
            <summary>Afficher les produits</summary>
            <?php if(!empty($content) && !empty($content['produits'])) : foreach($content['produits'] as $key => $produit){?>
                <div id="produits_<?=$produit['id'];?>" class="div_det">
                    <button class="adm_delete_btn">X</button>
                    <?php foreach($produit as $key => $value){
                         if(!in_array($key, ['id_marque', 'id'])){ ?>
                            <div id="<?=$produit['id'] . '_' . $key . '_produits';?>" class="<?=$key;?>">
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

    <section id="add_produits_marques_promotions">
        <div id="add_marques" class="add_container">
            <button id="add_marques_btn" class="add_btn">+ Ajouter une marque</button>
            <div id="add_marques_form_container" class="add_form_container">
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
        </div>
        <div id="add_produits" class="add_container">
            <button id="add_produits_btn" class="add_btn">+ Ajouter un produit</button>
            <div id="add_produits_form_container" class="add_form_container">
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
        </div>
        <div id="add_promotions" class="add_container">
            <button id="add_promotions_btn" class="add_btn">+ Ajouter une promotion</button>
            <div id="add_promotions_form_container" class="add_form_container">
                <form method="post" action="" id="add_promotions_form" class="add_form">
                    <button class="close_form_btn">X</button>
                    <h3>Nouvelle promotion</h3>
                    <label for="id_marque">Marque :</label>
                    <select name="id_marque" id="select_marques">
                        <option value="all_marques" id="select_marques_all" selected>Toutes les marques</option>
                        <?php foreach($content['marques'] as $key => $marque){?>
                            <option value="<?=$marque['id'];?>"><?=$marque['nom'];?></option>
                        <?php } ?>
                    </select>
                    <label for="id_produit">Produit :</label>
                    <select name="id_produit" id="select_produits">
                        <option value="all_produits" id="select_produit_all">Tous les produits</option>
                    </select>
                    <label for="nom">Nom de la promotion :</label>
                    <input type="text" name="nom" required>
                    <label for="pourcentage">Pourcentage de réduction : <span id="show_discounted_price"></span></label>
                    <input type="number" name="pourcentage" required>
                    <label for="debut">Date de début :</label>
                    <input type="date" name="debut" required>
                    <label for="fin">Date de fin :</label>
                    <input type="date" name="fin" required>
                    <input type="submit" name="Ajouter">
                </form>
            </div>
        </div>
    </section>

    <section id="search_marques_container" class="search_item_container">
        <div id="search_marques_box" class="search_item_box">
            <button id="search_marques_close_btn" class="search_close_btn"><i class="fas fa-chevron-down"></i></button>
            <input type="text" name="adm_search" class="adm_search_input" id="adm_search_input_marques"> 
            <div class="search_results_box" id="search_results_marques">
                
            </div>
        </div>
    </section>

    <section id="search_produits_container" class="search_item_container">
        <div id="search_produits_box" class="search_item_box">
            <button id="search_produits_close_btn" class="search_close_btn"><i class="fas fa-chevron-down"></i></button>
            <input type="text" name="adm_search" class="adm_search_input" id="adm_search_input_produits">
            <div class="search_results_box" id="search_results_produits">

            </div>
        </div>
    </section>

    <section id="search_promotions_container" class="search_item_container">
        <div id="search_promotions_box" class="search_item_box">
            <button id="search_promotions_close_btn" class="search_close_btn"><i class="fas fa-chevron-down"></i></button>
            <input type="text" name="adm_search" class="adm_search_input" id="adm_search_input_promotions">
            <div class="search_results_box" id="search_results_promotions">

            </div>
        </div>
    </section>

    <a href="admin" id="back_btn">Retour</a>
</main>