$(function(){

    var optionCount = 0;

    // Form status filter
    $('.form-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/form';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.form-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/form';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Form submit.
    $('#form-save-submit').click(function(event){
        // First save form items
        // Submit
        $.ajax({
            url: "/admin/ajax/save-form-items",
            type: "POST",
            data: 'item=' + JSON.stringify($('#form-item-form').serializeArray()) + '&form=' + $('#formName').attr('rel'),
            dataType: "json"
        })
            .done(function(data){
                if (data.meta.code == 200 && data.meta.error == '') {
                    // Ok, good to submit the form as well.
                    $('#client-form-form').trigger('submit');
                } else {
                    alert('Save form item failed: ' + data.meta.error);
                }
            })
            .fail(function(jqXHR, textStatus) {
                alert("Save form item request failed: " + textStatus);
            });
    });

    // Delete the form
    $('#form-delete').click(function(event){
        var $button = $(event.target);
        // Change label and set id to empty
        $('#delete-modal').modal('show');
    });

    // Delete form request
    $('.delete-modal-submit').click(function(event){
        var pageFormId = $('#formName').attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-form",
            type: "POST",
            data: {id: pageFormId},
            dataType: "json"
        })
            .done(function(data){
                if (data.meta.code == 200 && data.meta.error == '') {
                    window.location.reload();
                } else {
                    alert('Delete form failed: ' + data.meta.error);
                }
            })
            .fail(function(jqXHR, textStatus) {
                alert("Delete form request failed: " + textStatus);
            });
    });

    // Add new FormItem
    $('#add-form-item').click(function(event){
        optionCount = 0;
        $('.form-item-option').remove();
        $('#add-form-item-option').hide();
        $('#add-form-item-modal').modal('show');


    });

    $('#add-to-form').click(function(event) {
        var $formItemVal = $('#form-item-type :selected').attr('id');
        var $formItemOrder = $('#form-item-order').val();
        var $formItemLabel = $('#form-item-label').val();
        var $formId = $('#formName').attr('rel');
        var $formOptions = JSON.stringify($('#form-item-form').serializeArray());
        $.ajax({
            url: "/admin/ajax/add-form-item",
            type: "POST",
            data: {form_id:$formId, order:$formItemOrder, data:$formOptions, control_type:$formItemVal, text:$formItemLabel},
            dataType: "json"
        })
            .done(function(data){
                if (data.meta.code == 200 && data.meta.error == '') {
                    window.location.reload();
                } else {
                    alert('Add form item failed: ' + data.meta.error);
                }
            })
            .fail(function(jqXHR, textStatus) {
                alert("Add form item request failed: " + textStatus);
            });

        $('#add-form-item-modal').modal('hide');
    });

    // Delete form item request
    $('.delete-form-item-button').click(function(event){
        var formItemId = $(this).attr('rel');
        // Submit
        $.ajax({
            url: "/admin/ajax/delete-form-item",
            type: "POST",
            data: {id: formItemId},
            dataType: "json"
        })
            .done(function(data){
                if (data.meta.code == 200 && data.meta.error == '') {
                    window.location.reload();
                } else {
                    alert('Delete form item failed: ' + data.meta.error);
                }
            })
            .fail(function(jqXHR, textStatus) {
                alert("Delete form item request failed: " + textStatus);
            });

    });

    // Show additional option form
    $('#add-form-item-option').click(function(event) {
        optionCount++;

        newOptionLabel = $("<label class='form-item-option'></label>")
            .attr("for", "form-item-option-" + optionCount)
            .text("Option " + optionCount + ": ");
        newOption = $("<input type='text' value='' />")
            .attr("class", "form-control form-item-option")
            .attr("name", "form-item-option-" + optionCount);

        $(".form-control :last").after(newOptionLabel);
        newOptionLabel.after(newOption);
    });

    $('#form-item-type').change(function ()
    {
        if($('#form-item-type option:selected').attr("id") == "fi-text-input" || $('#form-item-type option:selected').attr("id") == "fi-value-input"){
            $('#add-form-item-option').hide();
        } else {
            $('#add-form-item-option').show();
        }
    });



});