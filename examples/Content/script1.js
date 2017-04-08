var searchDiv; 
var defaultDiv;
var allotDiv;
var access_token;
var user_id;

jQuery(document).ready(function ($) {
	access_token = $("#token").attr("token");
	user_id = $("#user_id").attr("user_id");
	searchDiv = $(".search").detach(); 
	allotDiv = $(".allot").detach();
	defaultDiv = $(".default");
	// $("#textArea").keypress(function(e){
	// 	if(e.keyCode==13){
	// 		e.preventDefault();
	// 		sendMessage();
	// 	}
	// });

	$("#history").scroll(function(){
		if(this.scrollTop==this.scrollHeight-this.clientHeight)
		{
				loadMessage($("#myTable > tbody > tr:last").attr("message_id"));
		}
			});
});

function loadMessage(id){
	$.ajax({
		url : '/parsers/parser.php' ,
		method : 'GET' ,
		data : {
			action : 'load',
			id: user_id,
			message_id: id,
			type: "message",
			access_token: access_token
		},
		success: function(rez){
			var t =rez;
			$("#myTable > tbody").append(rez);
			}
      });
}


function sendMessage(){
	if(($("#textArea").val()!="")||($("#attachment>").length!=0))
	{
		var message = $("#textArea").val();
		$("#textArea").val("");
		var data=$("#attachment>").detach();
		$.ajax({
			url : '/parsers/parser.php' ,
			method : 'GET' ,
			data : {
				action : 'send',
				id: user_id,
				type: "message",
				message: message,
				attachment:$("#attachment").attr("attachment"),
				access_token: access_token
			},
			success: function(rez){
				$("#myTable > tbody").prepend(rez);
				$("#forAttachments").replaceWith(data);
        	 // var t = rez;
        	}
        });
	}
}

function allotDel(){
	$(".active").removeClass("active");
	defaultActive();
}

function allotMessage(el) {
	if(!$(el).hasClass("disabled"))
		$(el).toggleClass("active");
	if($(".active").length==0)
		defaultActive();
	else
		allotActive();
	$("#countMessage").text($(".active").length+" сообщ.");
};

function searchActive(){
	searchDiv.appendTo($("#div1"));
	$(".default").detach();
	$(".allot").detach();
}

function allotActive(){
	allotDiv.appendTo($("#div1"));
	$(".search").detach();
	$(".default").detach();
}

function defaultActive(){
	defaultDiv.appendTo($("#div1"));
	$(".search").detach();
	$(".allot").detach();
}

function delMessage() {
	$(".active").each(function (){del($(this).attr("message_id"))});
}

function del(id){
	
	$.ajax({
		url : '/parsers/parser.php' ,
		method : 'GET' ,
		data : {
			action : 'delete',
			id: id,
			type: "message",
			access_token: access_token
		},
		success: function(rez){
			str="[message_id="+id+"]";      
        		 // data = "'"+$(str).find(".bodyMessage").html()+"'";    		
        		 if(rez==1)
        		 {            		   
        		 	var data = $(str).find(".bodyMessage >").detach(); 
        		 	$(str).addClass("disabled");   		
        		 	$(str).removeClass("active");
        		 	defaultActive();
            		// $(str).find(".err").html('');
            		$(str).find(".bodyMessage").append('<a href="#" class="restore" >Востановить</a>');
            		$(str).find(".restore").click( function(){ restoreMessage(id,data); });
            	}
            // else
            // 	$(str).find(".err").html('<span onclick="del('+id+','+access_token+')" class="glyphicon glyphicon-warning-sign"></span>');

          }
        }); 
}

function restoreMessage(id,data){
	$.ajax({
		url : '/parsers/parser.php' ,
		method : 'GET' ,
		data : {
			action : 'restore',
			id: id,
			type: "message",
			access_token: access_token
		},
		success: function(rez){
			str="[message_id="+id+"]";
			if(rez==1)
			{
				$(str).removeClass("disabled");
				$(str).find(".bodyMessage").empty();
				$(str).find(".bodyMessage").append(data);
			}
            // else
            // 		$(str).find(".bodyMessage").prependTo('<span onclick="restoreMessage('+id+','+data+','+access_token+')" class="glyphicon glyphicon-warning-sign"></span>');

          }
        });

}

function getAttachments(media_type)
{
	$.ajax({
		url : '/parsers/parser.php' ,
		method : 'GET' ,
		data : {
			id: user_id,
			action : 'getAttachments',
			media_type:media_type,
			access_token: access_token
		},
		success: function(rez){
						$("div.modal-body").empty();
           	$("div.modal-body").append(rez);
          }
        });
}


	function uploadFile(attachment,htmlCode){
		$("#attachment").append(htmlCode);
		$("#attachment").attr("attachment",attachment);
		var z =$("#attachment");
}
