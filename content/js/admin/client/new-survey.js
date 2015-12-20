$(function(){
    $('#survey-save-submit').click(function(event){
        $('#client-survey-form').trigger('submit');
    });
	
	//hideTitles();
	hidePages();
	
	//clientDropDownEvent_FilterTitle();
	//titleDropDownEvent_FilterPage();
	
	clientDropDownEvent_FilterPage();
	
});