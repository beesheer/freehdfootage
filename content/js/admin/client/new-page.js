$(function(){
    $('#page-save-submit').click(function(event){
        $('#client-page-form').trigger('submit');
    });
	showHideSurveyDropDown(false);

    // Page navigation element.
    $('#navigation').parent().append('<div id="navigation_ui"></div>');
    $('#navigation_ui').pageNavigationUi({items: pageNavItems});

    // Audio file upload
    $('#client').bind('change', function(e){
        $('#audioUpload').fileupload('option', 'formData', {page: 0, client: $('#client').val()});
    });
    $('#audioUpload').fileupload({
        formData: {page: 0, client: $('#client').val()},
        dataType: 'json',
        url: '/admin/ajax/audio-upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Audio file upload faield: ' + data.result.meta.error);
            } else {
                $('#audio_url').val(data.result.audio)
            }
        }
    });
});

function showHideSurveyDropDown(bool) {
	if(bool==true) {
		$("#survey").show();
		$('label[for=survey]').show();
	} else {
		$("#survey").hide();
		$('label[for=survey]').hide();
	}
}