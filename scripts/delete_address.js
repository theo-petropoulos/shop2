$(function(){
    $(document).on('click', '.delete_address', function(){
        let id = $(this).parent().attr('id').replace('address_', '')
        let authtoken = Cookies.get('authtoken')
        $.post(
            '/shop/controller/data/JSHandler.php',
            {delete:'address', id, authtoken},
            (res)=>{
                console.log(res);
                switch(res){
                    case 'success':
                        $("#registered_addresses").load(location.href + " #registered_addresses");
                        break;
                    case 'no_effect':
                        $("#registered_addresses").prepend('<p>Une erreur est survenue. Veuillez contacter le support à l\'adresse \
                        <a href="mailto:support@minimal-shop.com">support@minimal-shop.com</a></p>.');
                        break;
                    case 'error':
                    default:
                        $("#registered_addresses").prepend('<p>Une erreur inattendue est survenue</p>.');
                        break;
                }
            }
        )
    })
})