$(function(){

	$("#launch-my-presentations").click(function(event){

		console.log("MY PRESENTATION BUTTON EMAIL ADDRESS = "+email+"  CLIENT ID = "+clientId);

		window.open( "/folio/framework/index.html?email="+email+"&clientId="+clientId, "_blank", "width=1024, height=768, top:0, left:0");

	});


});