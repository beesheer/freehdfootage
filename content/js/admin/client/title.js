$(function(){
    // Page status filter
    $('.title-status-filter').click(function(event){
        event.preventDefault();

        var clientId = parseInt($('#client-filter-default').attr('rel'));
        var status = $(event.target).attr('rel');
        var link = '/admin/client/title';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });

    // Client filter
    $('.title-client-filter').click(function(event){
        event.preventDefault();

        var clientId = $(event.target).attr('rel');
        var status = $('#status-filter-default').attr('rel');
        var link = '/admin/client/title';
        if (clientId > 0) {
            link += '/client/' + clientId;
        }
        if (status > 0) {
            link += '/status/' + status;
        }
        window.location = link;
    });
    
    
    // table sorter
    if ($("tbody tr").length > 0) {
            
        // for sorting dates columns 
        var months = {
            "January"   : 0,
            "February"  : 1,
            "March"     : 2,
            "April"     : 3,
            "May"       : 4,
            "June"      : 5,
            "July"      : 6,
            "August"    : 7,
            "September" : 8,
            "October"   : 9,
            "November"  : 10,
            "December"  : 11
        };
        
        // add parse function for dates
        $.tablesorter.addParser({ 
            id: 'sort_date_column', 
            is: function(s) { 
                // return false so this parser is not auto detected 
                return false; 
            }, 
            format: function(s) {
                // April 4, 2014
                // d1: " " first
                // d2: ","
                var d1 = s.indexOf(" ");
                var d2 = s.indexOf(",");
                // months[April] = 3
                var month = months[s.substring(0, d1)];
                // 4
                var day = parseInt(s.substring(d1 + 1, d2));
                // 2014
                var year = s.substr(d2 + 2, 4);
                
                var newDate = new Date(year, month, day, 0, 0, 0, 0);
                var timeInMillis = newDate.getTime();
                return timeInMillis;
            }, 
            // set type, either numeric or text 
            type: 'numeric' 
        }); 
        
        // default sort first column
        $(".table").tablesorter({
            sortList: [[0,0]],
            headers: { 
                2: {
                    sorter:'sort_date_column' 
                },
                3: {
                    sorter:'sort_date_column' 
                }
            } 
        });
    }
});