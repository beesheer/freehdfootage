
function printStudentList() {
    var getFields = printFieldsAddendum();
    var URL = (document.URL.indexOf("export")!= -1 ) ? document.URL.substring(0,document.URL.indexOf("export")-1) : document.URL;
    var printOptions = ( getFields == "") ? "" : "/studentprintfields/" + getFields.replace(/\//g, ' or ');
    var newlink =  URL + "/export/" + currentTab.substring(1) + printOptions;
    $('#studentPrintOptionsModal').modal('hide');
    window.location = newlink;
}

$(function(){

    $(".table").show();
    var currentTab = "#studentlist";
    //panel tabs
    $('#resultQuizTabs a').click(function (e) {
        e.preventDefault();
        if($(this).text() != "Export to Excel") {
            currentTab =  $(e.target).attr('href') ;
            $(this).tab('show');
        } else {
            var URL = (document.URL.indexOf("export")!= -1 ) ? document.URL.substring(0,document.URL.indexOf("export")-1) : document.URL;
            var newlink =  URL + "/export/" + currentTab.substring(1);
            window.location = newlink;
        }
    })




});