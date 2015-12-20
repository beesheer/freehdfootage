/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){

    $("#menu-config").click(function(event) {
        event.stopPropagation();
        if ($("#menu-config-dropdown").css("display") == "none")
            $("#menu-config-dropdown").stop().slideDown();
        else
            $("#menu-config-dropdown").stop().slideUp();
    });

	$("#menu-config-dropdown").hover(function(){

	},function(){
		$("#menu-config-dropdown").stop(true,true).slideUp();
	});

	$(".config-item").click(function(e){
		console.log(e.currentTarget.innerHTML);
	});
});
