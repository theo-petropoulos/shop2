window.search='';
window.selector=0;

$(function(){
    //Set focus on search bar
    $("#search_input").focus();

    //Catch enter and delete keys
    $(document).on({
        'keydown':function(e){
            if(e.which=='13' && selector==0){
                $("#search_submit").trigger('click')
            }
            else if(e.which=='13' && selector!==0){
                e.preventDefault()
                clog($(".searchp:nth-child("+selector+")"))
                $("#search_input").val($("#search_input").val() + $(".searchp:nth-child("+selector+")").find('span').html())
                $("#search_submit").trigger('click')
            }
            if(e.which=='46' || e.which=='8'){
                //
                // ACTION ON DELETE
                //
            }
            // if(e.keyCode==40){
            //     e.preventDefault()
            //     $(document).find('#searchp2').trigger('mouseenter')
            // }
        },
        'keyup':function(e){
            if(e.keyCode==40){
                if($('.searchp').length > selector) selector++;
                e.preventDefault()
                $('.searchp:nth-child('+(selector-1)+')').css('background', 'initial')
                $('.searchp:nth-child('+selector+')').css('background', 'rgb(240, 240, 240)')
            }
            else if(e.keyCode==38){
                if(selector > 0) selector--;
                e.preventDefault()
                $('.searchp:nth-child('+(selector+1)+')').css('background', 'initial')
                $('.searchp:nth-child('+selector+')').css('background', 'rgb(240, 240, 240)')
            }
            else{
                let clear=setTimeout(() => {
                    $("#results_box").empty();
                }, 100);
                complete = $('#search_input').val();
                if(complete.length>0){
                    var post = 
                    $.post(
                        '/autocompletion/php/search.php',
                        {
                            complete
                        })
                    .done(function(data, status){
                    try{
                        let results=JSON.parse(data);
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
    }, "#search_input");

    $(document).on({
        'mouseenter': function(){
            $(this).css({
                "background":"rgb(240, 240, 240)",
                "transition":"0.3s"
            })
        },
        'mouseleave': function(){
            $(this).css({
                "background":"initial",
                "transition":"0.3s"
            })
        }
    }, '.searchp')
});

function clog(x){
    return console.log(x);
}