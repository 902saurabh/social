$(document).ready(function(){


$("#submit_profile_post_button").click(function(){
	$.ajax({
		type: "POST",
		url: "include/handlers/ajax_submit_profile_post.php",
		data: $("form.profile_post").serialize(),
		success: function(msg){
			$("#post_form").modal("hide");
			location.reload();
		},
		error: function(){
			alert('failure');
		}
	})

	})


});

function getUsers(value,user){
	$.post("include/handlers/ajax_friend_search.php",{query:value , userLoggedIn:user},function(data){
		$(".results").html(data);
	});

}