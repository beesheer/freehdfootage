$(function(){

    $('#page-save-submit').click(function(event){
        $('#client-page-form').trigger('submit');
    });

	showHideSurveyDropDown(false);

    // Page navigation element
    $('#navigation').parent().append('<div id="navigation_ui"></div>');
    $('#navigation_ui').pageNavigationUi({items: pageNavItems});

    // Audio file upload
    $('#client').bind('change', function(e){
        $('#audioUpload').fileupload('option', 'formData', {page: 0, client: $('#client').val()});
    });
    $('#audioUpload').fileupload({
        formData: {page: 0, client: $('#client').val()},
        dataType: 'json',
        url: '/client/ajax/audio-upload',
        done: function (e, data) {
            if (data.result.meta.error != '') {
                alert('Audio file upload faield: ' + data.result.meta.error);
            } else {
                $('#audio_url').val(data.result.audio)
            }
        }
    });


    clientNameSlug = filterTextToSlug( client.name );

    //auto-populate the page id
    $('#page_id').attr('readonly', true);
    $('#name').on('input', function() {
        var slug = filterTextToSlug( $("#name").val() );
        var hash = (Math.random().toString(36)+'00000000000000000').slice(2, 7);
        $("#page_id").val(clientNameSlug + "_" + slug + "_" + hash);
    });

    //client side pages are based upon templates
    setPageType('template');

    //add a tip to the error field
    $('#page_id').next('span').after('<span id="pop" href="#" class="tip-question" data-toggle="popover" data-content="Your page name may be identical to another page. Try editing the page name." data-original-title="" title="">?</span>');

    $('[data-toggle="tooltip"]').tooltip({
        'placement': 'top'
    });
    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        'placement': 'top'
    });

});

function setPageType( type ) {
    $("#type").val(type);
}

function showHideSurveyDropDown(bool) {
	if(bool==true) {
		$("#survey").show();
		$('label[for=survey]').show();
	} else {
		$("#survey").hide();
		$('label[for=survey]').hide();
	}
}
var clientNameSlug;

function filterTextToSlug( value ) {
    var mainText = value.split("");
    var slug = "";
    for (letter in mainText) {
        if (/[a-zA-Z0-9]/.test(mainText[letter])) {
            if (mainText[letter] === mainText[letter].toUpperCase())
                slug += mainText[letter].toUpperCase();
        }
    }
    return slug;
}