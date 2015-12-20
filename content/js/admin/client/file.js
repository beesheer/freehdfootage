function filterFileUploadName(name) {
	return name.split(/(\\|\/)/g).pop();
}

function getFileDeleteBtn(table,column,n) {
	var html = '<button type="button" style="display:inline-block;opacity:0.6;" class="close delete-file-button" onclick="deleteFile(this,\''+table+'\',\''+column+'\','+n+')">×</button>';
	return html;
}

function deleteFile(btnid,table,column,n) {
	var filename = $(btnid).next('p').html();
	var confirmation = confirm("Delete " + filename + " from this question?");
	if(confirmation==true) {
		var postdata = new FormData();
		postdata.append( 'questionid',$("#add-question-form").find("#id").val() );
		postdata.append( 'clientid',$("#form-modal").find("#client").val() );
		postdata.append( 'filename', filename );
		postdata.append( 'table', table );
		postdata.append( 'column', column );
		if(table=="option") {
			postdata.append( 'order', n );
		}
		if(table=="question") {
			
		}
		$.ajax({
            url: "/admin/ajax/delete-file",
            type: "POST",
			cache: false,
    		contentType: false,
			processData: false,
            data: postdata,
           	dataType: "json"
        })
        .done(function(data){
            if (data.meta.code == 200 && data.meta.error == '') {
				alert('File deleted');
				//refresh page:
				window.location.reload();
            } else {
               alert('Delete file failed: ' + data.meta.error);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) { 
            alert("Delete file request failed: " + textStatus);
        });
	}
}
/*
//http://stackoverflow.com/questions/19524118/calculate-multiple-files-length-for-ajax-upload-progress
*/
// pass the name of the div holding the file upload controls
function uploadFiles(ctrId,postdata) {
	totalSize = 0;
	// builds a set of INPUT controls and tries to process any files
	var uploadControls = $("#"+ctrId).find( "input:file" );
	for(var i = 0; i< uploadControls.length; i++) {
	   processFiles(uploadControls[i],(i+1),postdata);
	}
}
function processFiles(ctrl,n,postdata) {
		// if the control has files
		if(ctrl.files) {
		for(var i=0; i < ctrl.files.length; i++) {
			var file = ctrl.files[i];
			totalSize += file.size;
			postdata.append("file" + n, file);
		}
	}
}