$(function(){
    
    // Remove all the portal links
    $('a.portalLinks').attr('href', 'javascript:void(0)');


    $('.activatePortal').bind('click', function(){
        $('.activatePortal').removeClass('success');
        $(this).addClass('success');

        var pId = $(this).attr('rel');
        // Change the links for the nav tabs
        $('#portal-nav-pages a').attr('href', '/admin/review-portal/pages/id/' + pId);
        $('#portal-nav-comments a').attr('href', '/admin/review-portal/comments/id/' + pId);
        $('#portal-nav-pdf a').attr('href', '/admin/review-portal/pdf/id/' + pId);
        $('#portal-nav-details a').attr('href', '/admin/review-portal/details/id/' + pId);
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
                // April 4, 2014 08:12 am
                // d1: " " first
                // d2: ","
                // d3: ":"
                // d4: " " last
                var d1 = s.indexOf(" ");
                var d2 = s.indexOf(",");
                var d3 = s.indexOf(":");
                var d4 = s.lastIndexOf(" ");
                // months[April] = 3
                var month = months[s.substring(0, d1)];
                // 4
                var day = parseInt(s.substring(d1 + 1, d2));
                // 2014
                var year = s.substr(d2 + 2, 4);
                // 8
                var hour = parseInt(s.substr(d3 - 2, 2));
                // 12
                var minute = parseInt(s.substr(d3 + 1, 2));
                // am
                var ampm = s.substr(d4 + 1, 2);
                if (ampm == "pm")
                    hour = parseInt(hour) + 12;
                
                var newDate = new Date(year, month, day, hour, minute, 0, 0);
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
                1: {
                    sorter:'sort_date_column' 
                },
                2: {
                    sorter:'sort_date_column' 
                }
            } 
        });
    }
});