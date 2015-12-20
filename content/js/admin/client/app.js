$(function(){
    // Confirm to delete multiple apps.
    $('.delete-app').click(function(event){
        var apps = "";
        $(".select-app").each(function() {
            if ($(this).hasClass('checked')) {
                var appName = $(this).parent().parent().children(".app-name").html();
                if (apps.length == 0) {
                    apps += appName;
                } else {
                    apps += ", " + appName;
                }
            }
        });

        // confirm popup
        $('#delete-app-name').text(apps);
        $('#delete-modal').modal('show');

    });

    // Delete request
    $('.delete-modal-submit').click(function(event){

        var total = 0;
        var done = 0;

        $(".select-app").each(function() {
            if ($(this).is(':checked')) {
                total++;
            }
        });

        $(".select-app").each(function() {
            if ($(this).hasClass('checked')) {
                var appId = $(this).attr("appId");
                // Submit
                $.ajax({
                    url: "/admin/ajax/delete-app",
                    type: "POST",
                    data: {id: appId},
                    dataType: "json"
                })
                .done(function(data){
                    if (data.meta.code == 200 && data.meta.error == '') {
                        done++;
                        if (done == total) {
                            window.location.reload();
                        }
                    } else {
                        alert('Delete app failed: ' + data.meta.error);
                    }
                })
                .fail(function(jqXHR, textStatus) {
                    alert("Delete app request failed: " + textStatus);
                });
            }
        });
    });
});