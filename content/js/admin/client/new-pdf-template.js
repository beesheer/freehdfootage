$(function(){
    // CKEditor
    CKEDITOR.replace('template',{
        allowedContent: true
    });

    $('#save-submit').click(function(event){
        $('#client-pdf-template-form').trigger('submit');
    });
});