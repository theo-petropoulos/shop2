window.is_search = null
window.item = null
window.id = null
window.authtoken = Cookies.get('ADMauthtoken')

$(function(){
    changeProducts()
    changeDiscount()
    /**
     * Modify an item
     */
    $(document).on('click', '.adm_modify_button', function(){
        if($(this).parents('.search_results_box').length) is_search = 1
        let container = $(this).parent('div')
        let id_container = container.attr('id').split('_')
        let value = $(this).prev('p').text()

        id = id_container[0]
        item = id_container[1]
        window['prevHTML_' + item + '_' + id] = $(this).parents('div').html()
        if(!container.hasClass('nom_marque')){
            container.html(
                '<input type="text" name="' + item + '" value="' + value + '" required>\
                <span>\
                <button class="adm_modify_submit">Valider</button>\
                <button class="adm_modify_cancel">Annuler</button>\
                </span>'
            )
        }
        else{
            let marques = null
            $.post(
                '/shop/controller/data/JSHandler.php',
                {adm_fetch_marques:1, authtoken},
                (res)=>{
                    marques = JSON.parse(res)
                }
            )
            .done(()=>{
                container.html(
                    '<select id="select_marques_modify" name="id_marque"></select>\
                    <span>\
                        <button class="adm_modify_submit">Valider</button>\
                        <button class="adm_modify_cancel">Annuler</button>\
                    </span>'
                )
                for(let marque of marques){
                    $('#select_marques_modify').append(
                        '<option value="' + marque['id'] + '">' + marque['nom'] + '</option>'
                    )
                }
            })
        }
    })

    /**
     * Cancel the modification
     */
    $(document).on('click', '.adm_modify_cancel', function(){
        if(is_search){
            $(this).parents('div').first().html(window['prevHTML_' + item + '_' + id])
        }
        else{
            let container = $(this).parents('div')
            let id_container = "#" + container.attr('id')
            $(id_container).load(" " + id_container + " > *")
        }
    })

    /**
     * Submit the modification
     */
    $(document).on('click', '.adm_modify_submit', function(e){
        let proceed = 1
        let div = "#" + $(this).parents('div').first().attr('id')
        let id_div = $(this).parents('div').first().attr('id').split('_')
        let id = id_div[0]
        let item = id_div[2] === 'marque' ? 'id_marque' : id_div[1]
        let value = $(this).parents('div').first().find('input').val() ?? $(this).parents('div').first().find('select').val()
        let table = $(this).parents('details').first().length ? 
            $(this).parents('details').first().attr('id').replace('_det', '').split('_')[0] : 
            $(this).parents('div').first().attr('id').split('_')[2]
        if(item === 'fin' || item === 'debut'){
            var d = new Date(value);
            if (isNaN(d.getTime()))
                proceed = 0
            else{
                if(item === 'fin'){
                    let cmpvalue = $('#' + id + '_debut').find('p').text() ? 
                        $('#' + id + '_debut').find('p').text() :
                        $('#' + id + '_debut_promotions_search').find('p').text() 
                    if(cmpvalue > value)
                        proceed = 0
                }
                else if(item === 'debut'){
                    let cmpvalue = $('#' + id + '_fin').find('p').text() ?
                        $('#' + id + '_fin').find('p').text() :
                        $('#' + id + '_fin_promotions_search').find('p').text() 
                    if(cmpvalue < value)
                        proceed = 0         
                }
            }
        }
        if(proceed){
            $.post(
                '/shop/controller/data/JSHandler.php',
                {adm_modify:table, authtoken, id, item, value},
                (res)=>{
                    // console.log(res)
                }
            )
            .done(()=>{
                if(is_search){
                    let parent = $(this).parents('div').first()
                    $(this).parents('div').first().html(window['prevHTML_' + item + '_' + id])
                    parent.find('p').html(value)
                }
                else{
                    if(parseInt(id) != id){
                        let parent = $(this).parents('div').first()
                        $(this).parents('div').first().html(window['prevHTML_' + item + '_' + id])
                        parent.find('p').html(value)
                        parent.attr('id', value + '_nom')
                    }
                    else
                        $(div).load(" " + div + " > *")
                }
            })
        }
        else{
            $(div).find('p').last().remove()
            $(div).append('<p>La date est invalide.</p>')
        }
    })

    /**
     * Pop-up a window to create an item
     */
    $(document).on('click', '.add_btn', function(e){
        e.preventDefault()
        let item = $(this).next()
        item.css('visibility', 'visible')
    })

    /**
     * Close the pop-up
     */
    $(document).on('click', '.close_form_btn', function(e){
        e.preventDefault()
        let item = $(this).parents('.add_form_container')
        item.css('visibility', 'hidden')
    })
    $(document).on('click', '.add_form_container', function(e){
        if($(e.target).hasClass('add_form_container')) $(this).css('visibility', 'hidden')
    })

    /**
     * Submit the new item
     */
    $(document).on('submit', '.add_form', function(e){
        e.preventDefault()
        var fd = new FormData()
        let stringSer = decodeURIComponent($(this).serialize())
        let arr = stringSer.split('&')
        let form_id = $(this).attr('id').split('_')
        let table = form_id[1]
        if(table === 'produits'){
            var file = $('#image_form')[0].files
            if(file.length > 0 )
                fd.append('image',file[0])
        }
        $.each(arr, function(key, string){
            let elem = string.split('=')
            let item = elem[0]
            let value = elem[1]
            fd.append(item, value)
        })
        fd.append('adm_create', table)
        fd.append('authtoken', authtoken)
        $.ajax({
            url : '/shop/controller/data/JSHandler.php',
            type: "POST",
            data:fd,
            processData: false,
            contentType: false,
            success:function(res){
                console.log(res)
                if(res.indexOf('SUCCESS') >= 0)
                    location.reload()
                else if(res.indexOf('ERR_SQL_INSRT') >= 0)
                    alert('Une erreur est survenue lors de la mise à jour de la base de données. Veuillez contacter le support technique.')
                else 
                    alert('Une erreur inattendue est survenue. Veuillez contacter le support technique.')
            },
            error: function(jqXHR, textStatus, errorThrown){
                // console.log(textStatus, errorThrown)
            }
        })
    })

    /**
     * Delete an item
     */
    $(document).on('click', '.adm_delete_btn', function(){
        let resp = '';
        if($(this).parents('details').length){
            var container = '#' + $(this).parents('details').first().attr('id')
            var table = container.replace('#', '').replace('_det', '').split('_')[0]
            var id = $(this).parent().attr('id').replace(table + '_', '')
        }
        else{
            var container = '#' + $(this).parents('div').first().attr('id')
            var table = container.split('_')[0].replace('#', '')
            var id = container.split('_')[1]
            var search = 1
        }    
        if(table === 'marques'){
            var i = 0
            if(confirm('Attention : Supprimer une marque entrainera la suppression de tous les produits associés. Continuer ?')){
                i = 1
            }
        }
        if(typeof(i) == 'undefined' || i == 1){
            $.post(
                '/shop/controller/data/JSHandler.php',
                {adm_delete:table, id, authtoken},
                (res)=>{
                    resp = res
                }
            )
            .done(()=>{
                if(resp === 'SUCCESS'){
                    $('details').each(function(){
                        let id = '#' + $(this).attr('id')
                        $(id).load(' ' + id + ' > *')
                    })
                    if(typeof(search) !== 'undefined'){
                        $(this).parents('.div_det').first().remove()
                    }
                }
                else alert('Une erreur est survenue pendant la mise à jour de la base de données. Veuillez contacter le support technique.')
            })
        }
    })
    $(document).on('click', 'a[href="admin_delete_promotions"]', function(e){
        e.preventDefault()
        let nom = $(this).parents('details').first().attr('id').split('_')[1]
        if(confirm('Attention : Vous allez supprimer la promotion sur tous les produits affectés. Continuer ?')){
            $.post(
                '/shop/controller/data/JSHandler.php',
                {adm_delete_discount:1, nom, authtoken},
                (res)=>{
                    // console.log(res)
                    $('#promotions_det').load(' #promotions_det > *')
                }
            )
        }
    })

    /**
     * Show desired products according to the selected brand
     */
    $(document).on('change', '#select_marques', changeProducts)

    /**
     * Show actual discount on selected product
     */
     $(document).on('change', '#select_produits', changeDiscount)
     $(document).on('input', 'input[name=pourcentage]', changeDiscount)
})

function changeProducts(){
    let id_marque = $('#select_marques').find('option:selected').val()
    $.post(
        '/shop/controller/data/JSHandler.php',
        {adm_fetch_products:1, id_marque, authtoken},
        (res)=>{
            $('#select_produit_all').nextAll().remove()
            let products = JSON.parse(res)
            for(let i in products){
                $('#select_produits').append(
                    '<option value ="' + products[i]['id'] + '" price="' + products[i]['prix'] + '">' + products[i]['nom'] + '</option>'
                )
            }
        }
    )
}

function changeDiscount(){
    let produit = $('#select_produits').find('option:selected')
    let id_produit = produit.val()
    if(parseInt(id_produit) == id_produit){
        let price = produit.attr('price')
        let input_number = $('#add_promotions_form').find('input[type=number]').first()
        let discount = input_number.val()
        if(discount){
            let discounted_price = ( price - discount / 100 * price ).toFixed(2)
            $("#show_discounted_price").empty()
            $("#show_discounted_price").html('<del>' + price + '</del> => ' + discounted_price)
        }
        else $("#show_discounted_price").empty()
    }
    else $("#show_discounted_price").empty()
}