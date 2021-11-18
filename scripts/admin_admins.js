$(function(){
    /**
     * Show add admin form
     */
    $(document).on('click', '#add_admin_btn', function(){
        $(this).next().css({
            "visibility":"visible"
        })
    })

    /**
     * Close add admin form
     */
    $(document).on('click', '.close_form_btn', function(e){
        e.preventDefault()
        $(this).parents('div').first().css({
            "visibility":"hidden"
        })
    })

    /**
     * Submit add admin form
     */
    $(document).on('submit', '#add_admins_form', function(e){
        e.preventDefault()
        let inputs = $(this).find('input')
        let obj = {}
        let message = ''
        inputs.each(function(key, value){
            if(value.name !== 'Ajouter') obj[value.name] = value.value
        })
        let authtoken = Cookies.get('ADMauthtoken')
        $.post(
            '/shop/controller/data/JSHandler',
            {
                adm_create_adm:1, 
                login:obj['login'], 
                password:obj['password'], 
                cpassword:obj['cpassword'], 
                authtoken
            },
            (res)=>{
                // console.log(res)
                switch(res){
                    case 'ERR_PWD_STRG':
                        message = 'Le mot de passe n\'est pas assez fort. Pour rappel, il doit faire au moins 8 charactères de long, et contenir au moins :<br>\
                        - Une lettre majuscule<br>\
                        - Une lettre minuscule<br>\
                        - Un chiffre<br>\
                        - Un charactère spécial'
                        break;
                    case 'ERR_LOG_EXST':
                        message = 'Cet identifiant existe déjà.'
                        break;
                    case 'ERR_SQL_INSR':
                        message = 'Une erreur est survenue au niveau de la base de données. Veuillez contacter le support technique.'
                        break;
                    case 'SUCCESS':
                        message = 'Administrateur créé avec succès.'
                        break;
                    default:
                        message = 'Une erreur inattendue est survenue. Veuillez rafraichir la page puis réessayer. Sinon, contactez le support technique.'
                        break;
                }
            }
        )
        .done(()=>{
            $('#admins_list').load(' #admins_list > *')
            $(this).parents('div').first().css({
                "visibility":"hidden"
            })
            $('#admins_response').empty()
            $('#admins_response').append('<p>' + message + '</p>')
        })
    })

    /**
     * Delete admin
     */
    $(document).on('click', '.del_admin_btn', function(){
        let authtoken = Cookies.get('ADMauthtoken')
        let login = $(this).nextAll('p').first().text()
        let resp = '';
        let message = '';
        $.post(
            '/shop/controller/data/JSHandler.php',
            {adm_delete_adm:1, login, authtoken},
            (res)=>{
                // console.log(res)
                switch(res){
                    case 'ERR_IS_ADM':
                        message = 'Vous ne pouvez pas supprimer votre propre compte.'
                        break;
                    case 'ERR_SQL_DEL':
                        message = 'Une erreur est survenue au niveau de la base de données. Veuillez contacter le support technique.'
                        break;
                    case 'SUCCESS':
                        message = 'Administrateur supprimé avec succès.'
                        break;
                    default:
                        message = 'Une erreur inattendue est survenue. Veuillez rafraichir la page puis réessayer. Sinon, contactez le support technique.'
                        break;
                }
            }
        )
        .done(()=>{
            $('#admins_list').load(' #admins_list > *')
            $('#admins_response').empty()
            $('#admins_response').append('<p>' + message + '</p>')
        })
    })
})