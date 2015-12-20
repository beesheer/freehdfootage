
function printFieldsAddendum() {
    var valueArray = [];
    $("#printFieldOptions").find("tr").each( function() {
        if ( $(this).find('.checkbox').hasClass('checked') ) valueArray.push( $(this).attr("rel") );//.substring("printfield-".length)
    });
    return valueArray.join("|");
}

function printStudentList() {
    var getFields = printFieldsAddendum();
    var URL = (document.URL.indexOf("export")!= -1 ) ? document.URL.substring(0,document.URL.indexOf("export")-1) : document.URL;
    var printOptions = ( getFields == "") ? "" : "/studentprintfields/" + getFields;
    var newlink =  URL + "/export/" + currentTab.substring(1) + printOptions;
    $('#studentPrintOptionsModal').modal('hide');
    window.location = newlink;
}

$(function(){

    $(".table").show();

    //panel tabs
    $('#resultQuizTabs a').click(function (e) {
        e.preventDefault();
        if($(this).text() != "Export to Excel") {
            currentTab =  $(e.target).attr('href') ;
            $(this).tab('show');
        } else {
            var URL = (document.URL.indexOf("export")!= -1 ) ? document.URL.substring(0,document.URL.indexOf("export")-1) : document.URL;
            if( currentTab.substring(1) == "students" ) {
                $('#studentPrintOptionsModal').modal('show');
            } else {
                var newlink =  URL + "/export/" + currentTab.substring(1);
                window.location = newlink;
            }
        }
    })

    $('#resultQuizTabs a[href="' + currentTab + '"]').trigger("click");

    // calendar functions:
    // analytics date range
    $("#analytics-filter-date-from").click(function() {
        $("#calendar-arrow").css({
            "left": "150px"
        });

        var fromDate = getCorrectDate( $("#input-filter-date-from").val() );
        calendar.set( new Date( fromDate[0],Number(fromDate[1]-1),fromDate[2] ) );
        calendar.label = "from";
        calendar.show();
        $("#overlay, #calendar").show();
    });

    $("#analytics-filter-date-to").click(function() {
        $("#calendar-arrow").css({
            "left": "250px"
        });

        var toDate = getCorrectDate( $("#input-filter-date-to").val() );
        calendar.set( new Date( toDate[0],Number(toDate[1]-1),toDate[2] ) );
        calendar.label = "to";
        calendar.show();
        $("#overlay, #calendar").show();
    });

    $("#calendar-month-prev").click(function() {
        calendar.changeMonth(-1);
    });

    $("#calendar-month-next").click(function() {
        calendar.changeMonth(1);
    });

    $("body").delegate(".calendar-day", "click", function() {
        var date = $(this).html();
        var ordered = testChronological(calendar.label,new Date( calendar.year , (calendar.month) , date ));
        if(ordered) {
            calendar.changeDate(date);
            $("#analytics-filter-date-list .list-item").removeClass("selected");
            $("#overlay, #calendar").hide();
            $("#input-filter-date-" + calendar.label).val( calendar.year + "-" + (calendar.month+1) + "-" + calendar.date );
            $("#current-tab").val(currentTab);
            $("#form-date-range-filter" ).submit();
        }
    });

    function testChronological(field,date) {

        if(field=="to") {
            var fromDate = getCorrectDate( $("#input-filter-date-from").val() );
            var test = ( new Date( fromDate[0],Number(fromDate[1]-1),fromDate[2] ) <= new Date( date ) );
        } else {
            var toDate = getCorrectDate( $("#input-filter-date-to").val() );
            var test = ( new Date( date ) <= new Date( toDate[0],Number(toDate[1]-1),toDate[2] ) );
        }
        if(!test) {
            alert("Dates are not chronological.");
            $("#overlay, #calendar").hide();
        }
        return test;
    }

    function getCorrectDate(date_value) {
        var correctDate = ( date_value.indexOf(" ") != -1 ) ? date_value.substring(0,date_value.indexOf(" ")) : date_value ;
        correctDate = correctDate.split("-");
        return correctDate;
    }

});