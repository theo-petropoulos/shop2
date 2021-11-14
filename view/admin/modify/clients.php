<main id="admin">
    <div id="clients">
        <?php foreach($clients as $key => $client){ ?>
            <div id="client_<?=$client['id'];?>" class="client_div">
                <?php foreach($client as $key => $value){
                    if($key !== 'id'){ ?>
                        <div id="<?=$client['id'] . '_' . $key;?>" class="<?=$key;?>">
                            <p><?=$value != '' ? $value : '0'?></p>
                            <button class="adm_modify_button">Modifier</button>
                        </div>
                    <?php } 
                } ?>
                <div class="show_history">
                    <a href="admin?modify=clients&id=<?=$client['id'];?>">Afficher l'historique des achats</a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>