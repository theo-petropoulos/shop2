window.search = '';
window.item = '';
window.authtoken = Cookies.get('ADMauthtoken')

$(function(){
    $(document).on('click', '.trigger_adm_search', function(){
        item = $(this).parent().attr('id').split('_')[2]
        $('#search_' + item + '_container').css({
            "visibility":"visible"
        })
        $('#search_' + item + '_box').animate({
            bottom:"-20%"
        }, 600)
        $("#adm_search_input_" + item).focus()
    })

    $(document).on('click', '.search_close_btn', function(){
        console.log($(this).parent())
        $(this).parent().animate({
            bottom:"-100%"
        }, 600, function(){
            $(this).parent().css({
                "visibility":"hidden"
            })
        })
    })

    $(document).on({
        'keyup':function(e){
            let clear = setTimeout(() => {
                $("#search_results_" + item).empty()
            }, 100)
            let adm_search = $('#adm_search_input_' + item).val()
            console.log(item)
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
                    $("#results_box p").remove();
                    $(results).each(function(arrkey, object){
                        $("#results_box").prepend(
                            '<p class="searchp" id="searchp' + arrkey + '">' + complete + '<span class="chars_left">' + object['nom'].substr(complete.length) + '</span></p>'
                        );
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
        },
        'blur':function(){
            selector=0;
            $(document).off('click').on('click', function(e){
                if($(e.target).attr('id')!==$("#search_input").attr('id')) $("#results_box").empty();
                if($(e.target).is('.searchp, .searchp span')){
                    $(e.target).is('span') ? 
                        $("#search_input").val($("#search_input").val() + $(e.target).html()) :
                            $("#search_input").val($("#search_input").val() + $(e.target).find('span').html())
                }
            });
            $("#search_input").css({
                "border-bottom-right-radius":"8px",
                "border-bottom-left-radius":"8px"
            });
        },
        'focus':function(){
            if(search!==undefined) $("#search_input").trigger('keyup');
        }
    }, ".adm_search_input")
})