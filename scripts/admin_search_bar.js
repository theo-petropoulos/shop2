window.search = '';
window.item = '';
window.authtoken = Cookies.get('ADMauthtoken')

$(function(){
    /**
     * Open search bar
     */
    $(document).on('click', '.trigger_adm_search', function(){
        item = $(this).parent().attr('id').split('_')[2]
        $('#search_' + item + '_container').css({
            "visibility":"visible"
        })
        $('#search_' + item + '_box').animate({
            bottom:"0%"
        }, 600)
        $("#adm_search_input_" + item).focus()
    })

    /**
     * Close search bar
     */
    $(document).on('click', '.search_close_btn', function(){
        $(this).parent().animate({
            bottom:"-100%"
        }, 600, function(){
            $(this).parent().css({
                "visibility":"hidden"
            })
        })
    })
    $(document).on('click', '.search_item_container', function(e){
        if($(e.target).is('#search_marques_container') || $(e.target).is('#search_produits_container')){
            $(this).children().animate({
                bottom:"-100%"
            }, 600, function(){
                $(e.target).css({
                    "visibility":"hidden"
                })
            })
        }
    })
    
    $(document).on({
        'keyup':function(e){
            let clear = setTimeout(() => {
                $("#search_results_" + item).empty()
            }, 100)
            let adm_search = $('#adm_search_input_' + item).val()
            table = item
            if(adm_search.length > 0){
                var post = 
                $.post(
                    '/shop/controller/data/JSHandler.php',
                    {adm_search, table, authtoken},
                    (res)=>{
                        console.log(res)
                    })
                .done(function(data, status){
                try{
                    let results = JSON.parse(data);
                    let content = '';
                    $("#search_results_" + item + " div").remove();
                    $(results).each(function(arrkey, object){
                        $("#search_results_" + item).prepend(
                            "<div id='" + item + "_" + object['id'] + "_search' class='div_det'>\
                                <button class='adm_delete_btn'>X</button>\
                            </div>"
                        )
                        for(let key in object){
                            if(!['id', 'id_produit'].includes(key)){
                            $("#" + item + "_" + object['id'] + "_search").append(
                                "<div id='" + object['id'] + "_" + key + "_" + item + "_search' class='" + key + "'>\
                                    <h3>" + key + "</h3>\
                                    <p>" + object[key] + "</p>\
                                    <button class='adm_modify_button'>Modifier</button>\
                                </div>"
                            )
                            }
                        }
                    clearTimeout(clear);
                    });
                }catch (e){
                    return false;
                }
                if($("#results_box p").length>1){
                    $("#search_input").css({
                        "border-bottom-right-radius":"0",
                        "border-bottom-left-radius":"0"
                    });
                }
                })
                .fail(function(){
                    clog('not sent')
                })
            }
        }
    }, ".adm_search_input")

    /**
     * Auto show products by brand
     */
    $(document).on('click', 'a[href="admin_marques_show_products"]', function(e){
        e.preventDefault()
        let marque = $(this).attr('id').replace('_link', '')
        $('#adm_search_produits .trigger_adm_search').trigger('click')
        $('#adm_search_input_produits').val(marque)
        $('#adm_search_input_produits').trigger('keyup')
    })
})