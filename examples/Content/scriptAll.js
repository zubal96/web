var access_token;
jQuery(document).ready(function ($) {
	$("#myTable").tablesorter({});
	$("#filter").keyup(function () {
		$.uiTableFilter($("#myTable"), this.value);
	});
	access_token = $("#token").attr("token");
});

function buttonClick(el)
{
    var t;
    if (el!='')
       t='http://web-2/examples/main.php?flag='+el;
   else
    t= 'http://web-2/examples/main.php';
window.location.href= t ;
}

function star(el,id)
{
	var important;
	if($(el).hasClass("star"))
	{
		important = 0;
		$(el).removeClass("star")
	}
	else
	{
		important = 1;
		$(el).addClass("star")
	}
	$.ajax({
		url : '/parsers/parser.php' ,
		method : 'GET' ,
		data : {
			action : 'star',
			id: id,
			important: important,
			access_token: access_token
		}
        // success: function(data){
        //     alert(data);
        // }
      })
}

function delDialog(id){
     str ="tr[user_id="+id+"]";
     // el = $(str);
    if(confirm("Вы действительно хотите удалить всю переписку с данным пользователем? \r\n Отменить это действие будет невозможно."))
    $.ajax({
        url : '/parsers/parser.php' ,
        method : 'GET' ,
        data : {
            action : 'delete',
            id: id,
            type: "dialog",
            access_token: access_token
        },
        success: function(data){
            if(data==1)
            	if($(str).length)
            		$(str).remove();
            	else
            		buttonClick("");
                 
            else
                alert(data);
        }
    }); 
}

function answerDialog(el,id){
	var answered;
	if($(el).hasClass("answered"))
	{
		answered = 0;
		$(el).removeClass("answered")
	}
	else
	{
		answered = 1;
		$(el).addClass("answered")
	}
	$.ajax({
        url : '/parsers/parser.php' ,
        method : 'GET' ,
        data : {
            action : 'answer',
            id: id,
            answered: answered,
            access_token: access_token
        }
    }); 
}


