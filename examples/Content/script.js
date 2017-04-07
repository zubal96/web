
jQuery(document).ready(function ($) {
    mouse();
});

function mouse(){
    $("#myTable > tbody > tr").mouseenter(function(){
        $(this).addClass("active");
    })
    $("#myTable > tbody > tr").mouseleave(function(){
        $(this).removeClass("active");
    });
    $("#myTable > tbody > tr").mousedown( function(){
        enter(this);
    })
    $("#myTable > tbody > tr> td > span").mousedown( function(e){
        e.stopPropagation();
        // del($(this).closest("tr"));
    })
}

function enter(el)
{
    var t = 'http://web-2/examples/dialog.php?'+'user_id='+$(el).attr("user_id");
    window.location.href= t;
}



