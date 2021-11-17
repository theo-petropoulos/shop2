$(function(){
    /**
     * Modify an item
     */
    $(document).on('click', '.adm_modify_button', function(){
        let id_div = $(this).parent('div').attr('id').split('_')
        let value = $(this).prev('p').text()
        let id = id_div[0]
        let item = id_div[1]
        window['prevHTML_' + item + '_' + id] = $(this).parents('div').html()
        $(this).parent('div').html(
            '<input type="text" name="' + item + '" value="' + value + '" required>\
            <span>\
            <button class="adm_modify_submit">Valider</button>\
            <button class="adm_modify_cancel">Annuler</button>\
            </span>'
        )
    })

    /**
     * Cancel the modification
     */
    $(document).on('click', '.adm_modify_cancel', function(){
        let div = "#" + $(this).parents('div').first().attr('id')
        $(div).load(" " + div + " > *")
    })

    /**
     * Submit the modification
     */
    $(document).on('click', '.adm_modify_submit', function(){
        let div = "#" + $(this).parents('div').first().attr('id')
        let id_div = $(this).parents('div').first().attr('id').split('_')
        let id = id_div[0]
        let item = id_div[1]
        let value = $(this).parents('div').find('input').val()
        let authtoken = Cookies.get('ADMauthtoken')
        let table = $(this).parents('details').first().attr('id').replace('_det', '')
        console.log(div)
        $.post(
            '/shop/controller/data/JSHandler.php',
            {adm_modify:table, authtoken, id, item, value},
            (res)=>{
                console.log(res)
            }
        )
        .done(()=>{
            $(div).load(" " + div + " > *")
        })
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
        let authtoken = Cookies.get('ADMauthtoken')
        if(table === 'produits'){
            var file = $('#image_form')[0].files
            if(file.length > 0 )
                fd.append('image',file[0])
        }
        $.each(arr, function(key, string){
            let elem = string.split('=')
            let item = elem[0]
            let value = elem[1]
            console.log(item, value)
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
                location.reload()
            },
            error: function(jqXHR, textStatus, errorThrown){
            }
        })
    })

    /**
     * Delete an item
     */
    $(document).on('click', '.adm_delete_btn', function(){
        let container = '#' + $(this).parents('details').first().attr('id')
        let table = container.replace('#', '').replace('_det', '')
        let id = $(this).parent().attr('id').replace(table + '_', '')
        let authtoken = Cookies.get('ADMauthtoken')
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
                    console.log(res)
                }
            )
            .done(()=>{
                $('details').each(function(){
                    let id = '#' + $(this).attr('id')
                    $(id).load(' ' + id + ' > *')
                })
                // $(container).attr('open', '');
            })
        }
    })
})