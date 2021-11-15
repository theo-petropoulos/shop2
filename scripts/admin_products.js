$(function(){
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

    $(document).on('click', '.adm_modify_cancel', function(){
        let div = "#" + $(this).parents('div').first().attr('id')
        $(div).load(" " + div + " > *")
    })

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

    $(document).on('click', '.add_btn', function(e){
        e.preventDefault()
        let item = $(this).next()
        item.css('visibility', 'visible')
    })

    $(document).on('click', '.close_form_btn', function(e){
        e.preventDefault()
        let item = $(this).parent()
        item.css('visibility', 'hidden')
    })

    $(document).on('submit', '.add_form', function(e){
        e.preventDefault()
        var fd = new FormData()
        let stringSer = $(this).serialize()
        console.log(stringSer)
        let arr = stringSer.split('&')
        let form_id = $(this).attr('id').split('_')
        let table = form_id[1]
        let authtoken = Cookies.get('ADMauthtoken')
        if(table === 'produits'){
            var file = $('#image_form')[0].files
            if(file.length > 0 )
                fd.append('image',file[0])
        }
        console.log(arr)
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
                // location.reload()
            },
            error: function(jqXHR, textStatus, errorThrown){
            }
        })
        // $.post(
        //     '/shop/controller/data/JSHandler.php',
        //     {obj},
        //     (res)=>{
        //         console.log(res)
        //     }
        // )
    })
})