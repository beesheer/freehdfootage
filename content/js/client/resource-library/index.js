$(function(){
    var clientId = cookie.read("lifelearn-stratus-client");
    if (clientId == null || parseInt(clientId) == 0) {
        $('#uploadHandle').hide();
    } else {
        // Form modal preparation
        $('#client').val(clientId).prop('disabled','disabled');

    }

    // Click through the document items
    $('tr.noClickThrough').bind('click', function(event){
        loadResourceLibraryData($(this).attr("rel"));
        //event.preventDefault();
        //var url = '/client/resource-library/detail/id/' + $(this).attr('rel');
        //window.location.href = url;
        //$('#formModalLabel').text('Resource Library');        
    });

    function loadResourceLibraryData(doc_id)
    {
        $(".resourcelibrary_preview").find("img").css("display","none");
        $("#resourcelibrary_preview_pdf_iframe").css("display",""); 

        var api_url = "/client/resource-library/view";
        $.ajax({
                type: 'POST', 
                url: api_url, 
                data:{"id":doc_id},
                //customData:{appService:this},
                dataType:"json"
        }).done(function(data) {              
            $(".resource_library_title").text(data.name);
            $(".resource_library_file_type").text(data.file_type);
            $(".resource_library_date_added").text(data.created_datetime);
            $(".resource_library_tags").text(data.tags); 
            $("#resource_library_download_link").attr("href",data.download_link); 
            if(data.file_type=="PDF")
            {
                var pdf_ifreme_html='https://docs.google.com/gview?url='+data.preview_link+'&embedded=true';
                $("#resourcelibrary_preview_pdf_iframe").attr("src",pdf_ifreme_html); 
            }else
            {
                $("#resourcelibrary_preview_pdf_iframe").css("display","none"); 
                $(".resourcelibrary_preview").find("img").attr("src",data.preview_link);   
                $(".resourcelibrary_preview").find("img").css("display","");                 
            }
            $('#viewResourceLibraryRecord').modal('show');
        }).fail(function(jqXHR, textStatus) {
                console.log("ERROR data  "+jqXHR+"  textStatus = "+textStatus);

        });
    }

    /*function setSortOrder(order_by)
    {
        $("#search_order_by").val(order_by);
        searchResourceByFilter();
    }*/

});