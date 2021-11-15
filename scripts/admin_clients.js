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
        let id_div = "#" + $(this).parents('div').first().attr('id')
        $(id_div).load(" " + id_div + " > *")
    })

    $(document).on('click', '.adm_modify_submit', function(){
        let div = "#" + $(this).parents('div').first().attr('id')
        let id_div = $(this).parents('div').first().attr('id').split('_')
        let id = id_div[0]
        let item = id_div[1]
        let value = $(this).parents('div').find('input').val()
        let authtoken = Cookies.get('ADMauthtoken')
        $.post(
            '/shop/controller/data/JSHandler.php',
            {adm_modify:'clients', authtoken, id, item, value},
            (res)=>{
                console.log(res)
            }
        )
        .done(()=>{
        $(div).load(" " + div + " > *")
        })
    })
})