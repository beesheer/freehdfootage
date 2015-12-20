$(function(){
    // CKEditor
    CKEDITOR.replace('template',{
        allowedContent: true
    });

    $('#save-submit').click(function(event){
        $('#client-page-template-form').trigger('submit');
    });
});