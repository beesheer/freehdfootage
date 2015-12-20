$(function(){
    $('#title-save-submit').click(function(event){
        $('#client-title-form').trigger('submit');
    });

    /**
     * Filter out default option.
     *
     * @see STRAT-661
     * @author Bin Xu
     */
    $('#type option[value=default]').remove();
});