$(function(){

    var baseAjaxUrl = '/client/ajax-meeting';

    var currentMeetingId = '';

    var currentMeetingIdForContacts = ''; 

    var meetings = [];

    var meetingContactsArray = [];

    var contacts = [];

    var invitees = [];

    var uneditedInvitees = [];

    var titles = [];

    var allTitles = [];

    var llCalendar = LLCALENDAR;

    var llContactPicker = LLCONTACT_PICKER;

    var llAppService = LLAPP_SERVICE;

    var llFormatUtil = LLFORMAT_UTIL;

    var llDomUtil = LLDOM_UTIL;

    var inviteeInputId = "invitee_input_field";

    var autoCompleteSelectIndex = -1;

    var meetingStartDate = null;

    var promoteWindow = null;

    var afterGetContactsForMeetingCallback = null;


    function getData(){

        llAppService.getContacts( onGetContacts );

    }

    function getContactsForMeeting( meetingId, callback ){

        afterGetContactsForMeetingCallback = callback;

        llAppService.getContactsForMeeting( {id:meetingId}, onGetContactsForMeeting );

    }

    function onCreateNewMeeting(){
        meetingIdToUpdate = null;

        $('#formModalLabel').text('Create New Meeting');
        $('#form-commit-button').text('Start');
        $("#start-date-input").val( "Now" );
        $("#create-meeting-title-input").val("");
        $("#create-meeting-message-input").val("");

        invitees = [];

        updateInvitations();

        $('#form-modal').modal('show');

    }

    function updateMeeting(){

        //$("#form-commit-loading").show();
        //$("#form-commit-button").hide();

        var requiredFieldsValid = true;


        if( $("#create-meeting-title-input").val() == ""){
            requiredFieldsValid = false;

            $("#create-meeting-title-group").addClass("has-error");

        } else {
            $("#create-meeting-title-group").removeClass("has-error");
        }

        if( invitees.length == 0 ){
            requiredFieldsValid = false;

            $("#create-meeting-invite-group").addClass("has-error");
        } else {
            $("#create-meeting-invite-group").removeClass("has-error");
        }



        if( requiredFieldsValid ){
            $('#form-modal').modal('hide');

            $('#loading-modal').modal('show');

            var meetingObj = {};

            meetingObj.title = $("#create-meeting-title-input").val();


            meetingObj.start = new Date();

            if( meetingStartDate != null ){
                meetingObj.start = meetingStartDate;
            }

            meetingObj.contacts = [];

            for( var i=0; i < invitees.length; i++ ){

                var contact = invitees[i];

                meetingObj.contacts.push( contact.id );
            }

            meetingObj.message = $("#create-meeting-message-input").val();

            var startingTitle = $("#create-meeting-title-select").val();

            console.log(" startingTitle = "+startingTitle);

            if( startingTitle != "none" ){
                meetingObj.startingTitle = startingTitle;
            }

            var nowDate = new Date();

            console.log(" nowDate.getTime() = "+nowDate.getTime()+"   meetingObj.start = "+meetingObj.start.getTime() );

            promoteWindow = null;

            if( nowDate.getTime() >= meetingObj.start.getTime() ){

                promoteWindow = window.open( "", "_blank", "width=1024, height=768, top:0, left:0");

            }

            if( currentMeetingId != null ){

                meetingObj.id = currentMeetingId;

                llAppService.updateMeeting( meetingObj, onUpdateMeeting );

                $("#loading-modal-message").html( "Your meeting is being updated..." );

            } else {

                

                $("#loading-modal-message").html( "Your meeting is being created..." );

                llAppService.addMeeting( meetingObj, onAddMeeting );
            }


        }


    }



    function onAddMeeting( response ){

        console.log("ON ADD MEETING response = "+JSON.stringify(response, null, 4));

        

        onDoneUpdatingAndAddingMeeting( response );

    }

    function onUpdateMeeting( response ){

        if( response != null && response.error == null ){

            if( response.data != null && response.data.meeting != null ){
                removeMeetingContactsById( response.data.meeting.id );
            }

            $('#form-modal').modal('hide');
        }

        onDoneUpdatingAndAddingMeeting( response );

    }

    function onDoneUpdatingAndAddingMeeting( response ){

        $('#loading-modal').modal('hide');

        if( response != null && response.error == null && response.data != null && response.data.meta != null && response.data.meta.code == 200  ){

            console.log(" response.data.meeting = "+response.data.meeting)

            if( response.data.meeting != null ){

                if( promoteWindow != null ){
                    promoteWindow.location = "/promote/presenter/session/"+response.data.meeting.ukey;
                }


            } else {
                if( promoteWindow != null ){
                    promoteWindow.close();
                }
            }

            llAppService.getMeetings( onGetMeetings );
        }


    }

    function onDeleteMeeting( response ){

        if( response != null && response.error == null ){

            //$("#meeting-panel-"+currentMeetingId).remove();

            removeMeetingById(currentMeetingId);

            setupMeetingList();

            $('#delete-modal').modal('hide');
        }

    }

    function onCreateNewMeetingButton(){

        currentMeetingId = null;

        $('#formModalLabel').text('Create New Meeting');
        $('#form-commit-button').text('Start');
        $("#start-date-input").val( "Now" );

        $("#create-meeting-title-input").val("");

        $("#create-meeting-message-input").val("");

        $("#create-meeting-title-group").removeClass("has-error");
        $("#create-meeting-invite-group").removeClass("has-error");

        $("#create-meeting-title-select").val("none");

        getCustomTitles();

        //$("#form-commit-button").show();
        //$("#form-commit-loading").hide();

        meetingStartDate = new Date();
        llCalendar.setViewDate( meetingStartDate );

        invitees = [];
        updateInvitations();

        $('#form-modal').modal('show');


    }

    function onEditExistingMeetingButton(){

        currentMeetingIdForContacts = currentMeetingId;

        getContactsForMeeting( currentMeetingIdForContacts, readyToShowEditMeetingForm );

    }

    function readyToShowEditMeetingForm(){

        var meetingToEdit = getMeetingById( currentMeetingId );

        getCustomTitles();

        var nowDate = new Date();

        if( parseInt( meetingToEdit.start_datetime ) < nowDate.getTime() ){
            $("#start-date-input").val("Now");
        } else {
            $("#start-date-input").val( llFormatUtil.getDateToStringLongUTCFormat( new Date( parseInt(meetingToEdit.start_datetime) ) ) );


        }

        meetingStartDate = new Date( parseInt( meetingToEdit.start_datetime ) );

        llCalendar.setViewDate( meetingStartDate );



        if( meetingToEdit != null ){
            $("#create-meeting-title-input").val(meetingToEdit.subject);

            if( meetingToEdit.invite_message != null ){
                $("#create-meeting-message-input").val(meetingToEdit.invite_message);
            } else {
                $("#create-meeting-message-input").val("");
            }

        }

        if( meetingToEdit.starting_title == null ){
            $("#create-meeting-title-select").val("none");
        } else {
            $("#create-meeting-title-select").val(meetingToEdit.starting_title);
        }

        $("#create-meeting-title-group").removeClass("has-error");
        $("#create-meeting-invite-group").removeClass("has-error");



        invitees = getMeetingContactsById( currentMeetingId );

        uneditedInvitees[currentMeetingId] = invitees;

        if( invitees == null ){
            invitees = [];
        }
        updateInvitations();

        $('#formModalLabel').text('Edit Meeting');
        $('#form-commit-button').text('Update');
        $('#form-modal').modal('show');

    }

    function onGetContacts( response ){

        if( response != null && response.error == null ){

            contacts = response.data.contacts;

            if( meetings.length == 0 ){
                llAppService.getMeetings( onGetMeetings );
            }


        }
    }

    function onGetContactsForMeeting( response ){

        console.log("ON GET CONTACTS FOR MEETING = "+JSON.stringify( response, null, 4));

        if( response != null && response.error == null && response.data !=  null && response.data.contacts != null ){

            if( currentMeetingIdForContacts != null ){

                addMeetingContactsById( currentMeetingIdForContacts, response.data.contacts );

                if( afterGetContactsForMeetingCallback != null ){
                    afterGetContactsForMeetingCallback();
                }

            }

        }

    }

    function onGetMeetings( response ){

        if( response != null && response.error == null ){
            meetings = response.data.meetings;

            meetings = meetings.sort( function( a, b){
                return parseInt(a.start_datetime) - parseInt(b.start_datetime);

            });
        }

        setupMeetingList();

        updateInvitations();

        llAppService.getTitles( onGetTitles )

    }

    function onGetTitles( response ){

        console.log(" response = "+JSON.stringify( response, null, 4) );

        if( response != null && response.error == null ){
            titles = response.data.titles;


            getCustomTitles();


            

        }

    }

    function getCustomTitles(){

        var localStorageStore = new Persist.Store('Data Store');

        console.log(" localStorageStore = "+localStorageStore);

        allTitles = [];

                for( var i=0; i < titles.length; i++ ){
            allTitles.push( titles[i]);
        }

        //TEMP FIX TIL WE HAVe CUSTOM PRESENTATIONS SAVED BY THE USER
        var customUserText = localStorageStore.get("user/000.txt");

        var customTitles = [];
        if( customUserText != null && customUserText != "" ){
            var customUser = JSON.parse(customUserText);

            if( customUser != null ){
                customTitles = customUser.presentations;
                    }
        }

        console.log("CUSTOM TITLES = "+JSON.stringify( customTitles, null, 4));



        if( allTitles.length > 0 ){

            for( var ii = 0; ii < customTitles.length; ii++ ){

                var customTitle = customTitles[ii]; 

                var titleFound = false;

                for( var iii = 0; iii < allTitles.length; iii++ ){
                    var title = allTitles[iii];

                    if( title.id == customTitle.id ){
                        titleFound = true;
                        break;
                }
                }

                if( !titleFound ){
                    var newTitle = {
                        version:"1",
                        id:customTitle.id,
                        title:customTitle.title,
                        type:"presentation",
                        pageIds:customTitle.pageIds

            }

                    allTitles.push( newTitle );


        }
            }

            

    }

        setupStartingTitleDropDown();
    }

    function setupStartingTitleDropDown(){

        var options = "";

        options = "<option value='none'>None</option>";

        for( var i=0; i < allTitles.length; i++ ){
            var title = allTitles[i];

            if( title.type == "presentation" ){
                options += "<option value='"+title.id+"'>"+title.title+"</option>";
            }


        }

        $("#create-meeting-title-select").html( options );
    }

    function addContactById( contactId ){

        var selectedContact = null;

        for( var i=0; i < contacts.length; i++ ){

            var contact = contacts[i];

            if( contact.id == contactId ){
                selectedContact = contact;
                break;
            }

        }

        if( selectedContact != null ){

            var inviteeAlreadyAdded = false;

            for( var ii=0; ii < invitees.length; ii++ ){
                var invitee = invitees[ii];

                if( invitee.id == selectedContact.id ){
                    inviteeAlreadyAdded = true;
                    break;
                }

            }

            if( !inviteeAlreadyAdded ){
                invitees.push( selectedContact );

            }


        }


        updateInvitations();
    }

    function onAddedContactOnServer( response ){
        if( response.data != null && response.data.meta != null && response.data.meta.code != null && response.data.meta.code == 200 && response.data.contact != null ){
            contacts.push( response.data.contact );
            addContactById( response.data.contact.id );
        }


        //llAppService.getContacts( onGetContacts );

    }

    function addContactByEmail( email ){

        //var re = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?";


        var re = /\S+@\S+\.\S+/;

        if( re.test( email ) ){

            llAppService.addContact( { email:email }, onAddedContactOnServer );

        }


    }

    function removeContactById( contactId ){
        var selectedContact = null;

        for( var i=0; i < invitees.length; i++ ){

            var contact = invitees[i];

            if( contact.id == contactId ){
                invitees.splice( i, 1 );
                break;
            }

        }

        updateInvitations();
    }



    function setupMeetingListeners()
    {
        $(".meeting-toggle").click(function(event){

            var meetingToggleDiv = $(this);

            var meetingPanelDiv = meetingToggleDiv.parent();

            if(meetingPanelDiv.find("#meeting-title").hasClass("show-full-meeting-title")) {
                meetingPanelDiv.find("#meeting-title").removeClass("show-full-meeting-title");
            } else {
                meetingPanelDiv.find("#meeting-title").addClass("show-full-meeting-title");
            }

            var meetingPanelDivId = meetingPanelDiv.attr("id");

            currentMeetingIdForContacts = meetingPanelDiv.attr("id").replace( "meeting-panel-", "");


            meetingToggleDiv.removeClass("ion-arrow-right-b ion-arrow-down-b");

            var toHeight = 60;
            var ease = "easeOutExpo";
            var toArrowClass = "ion-arrow-right-b";
            var duration = 200;

            //alert("MEETING TOGGLE id = "+meetingPanelDivId+"  height = "+(meetingPanelDiv).height());

            if( $(meetingPanelDiv).height() <= 60 ){
                toHeight = 1000;
                duration = 500;
                ease = "easeInCirc";
                toArrowClass = "ion-arrow-down-b";
            }

            meetingToggleDiv.addClass(toArrowClass);


            $(meetingPanelDiv).stop( true, true ).animate({
                maxHeight: toHeight,
                ease:ease
              }, duration, function() {
                // Animation complete.
              });

            var meetingContacts = getMeetingContactsById( currentMeetingIdForContacts );

            if( meetingContacts == null ){
                getContactsForMeeting( currentMeetingIdForContacts, function(){

                    console.log("SUCCESS I GETTING CONTACTS FOR MEETING")

                    setupMeetingContacts( currentMeetingIdForContacts );

                } );
            } else {

                setupMeetingContacts( currentMeetingIdForContacts );

            }



        });

        $(".meeting-edit-button").click(function(event){

            var meetingEditButton = $(this);

            var meetingPanelDiv = meetingEditButton.parent();

            var meetingPanelDivId = meetingPanelDiv.attr("id");

            currentMeetingId = meetingPanelDiv.attr("id").replace( "meeting-panel-", "");

            onEditExistingMeetingButton();

        });

        $(".meeting-delete-button").click(function(event){

            var meetingEditButton = $(this);

            var meetingPanelDiv = meetingEditButton.parent();

            currentMeetingId = meetingPanelDiv.attr("id").replace( "meeting-panel-", "");


            $('#delete-modal').modal('show');

        });

        $(".meeting-play-button").click( function(){

            var meetingEditButton = $(this);

            var meetingPanelDiv = meetingEditButton.parent();

            currentMeetingId = meetingPanelDiv.attr("id").replace( "meeting-panel-", "");

            enterMeetingById( currentMeetingId );


        });
    }

    function setupListeners()
    {
        // Form submit.
        $('#create-new-meeting').click(function(event){

            onCreateNewMeetingButton();

        });



        $('#invitation-input-stage').click( function(){
            $('#'+inviteeInputId).focus();
        });



        $('.delete-modal-submit').click(function(event){

            llAppService.removeMeeting(  currentMeetingId, onDeleteMeeting );


        });

        $("#calendar-picker-button").click( function(event){

            llCalendar.showCalendarPicker( {x:280, y:250} );

        });

        $("#form-commit-button").click( function(event){

            uneditedInvitees[currentMeetingId] = invitees;
            updateMeeting();

        });

        $("#contact-picker-button").click( this, function(event){

            llContactPicker.showContactPicker( contacts, {x:280, y:420} );

        });

        $("#start-date-input").focus( function( event ){
            $("#start-date-input").blur();
            llCalendar.showCalendarPicker( {x:280, y:220} );

        })
    }



    function updateInvitations(){

        var inviteeHtml = "";

        for( var i=0; i < invitees.length; i++ ){
            var contact = invitees[i];

            var name = "";
            if( contact.firstname == null || contact.firstname == "" ){
                name = contact.email;
            } else {
                name = contact.firstname + " " + contact.surname;
            }

            inviteeHtml += '<p id="invitee_'+contact.id+'" class="emailBubble" style="cursor:pointer;">'+name+'<span class="ion-close emailBubbleArrow" style=""></span></p>';
        }

        inviteeHtml += '<input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="'+inviteeInputId+'" style="display:inline; border:none; float:left; outline:none;">';


        $("#invitation-input").html( inviteeHtml );

        $(".emailBubbleArrow").click( this, function( event ){

            var id = $(event.target).parent().attr("id");

            id = id.replace("invitee_", "");

            removeContactById( id );

        });

        $('#'+inviteeInputId).focus( function(){
            console.log(" INVITATION INPUT FOCUS");
        });

        $('#'+inviteeInputId).blur( function( ){
            console.log(" INVITATION INPUT BLUR = "+document.activeElement.tagName);

            if( document.activeElement.tagName == "BODY" ){

            }  else {
                
            }

            updateInviteeInput();
            hideAutoComplete();

        });

        $('#'+inviteeInputId).keyup(function (e) {

            if (e.keyCode == 13) {

                if( autoCompleteSelectIndex > -1 ){

                    var id = $(".llcontact-autocomplete-item:eq("+autoCompleteSelectIndex+")").attr("id");

                    id = id.replace("llcontact-autocomplete-item-id-", "");
                    addContactById( id );

                    hideAutoComplete();

                } else {
                    updateInviteeInput();
                }





            } else if( e.keyCode == 8 || e.keyCode == 46 ){
                removeInvitee();
            }  else if( e.keyCode == 38 ){

                autoCompleteSelect( "up" );

                e.stopPropagation();
            } else if( e.keyCode == 40 ){

                autoCompleteSelect( "down" );

                e.stopPropagation();
            }
        });

        $('#'+inviteeInputId).on('input', function(){


            var inputValue = $('#'+inviteeInputId).val();

            var lastCharacter = inputValue.substr( inputValue.length - 1, 1 );

            if( lastCharacter == "," || lastCharacter == ";" || lastCharacter == "|" ){

                updateInviteeInput();

            } else {
                $(".emailBubble:last").removeClass("emailBubbleSelected");
            }

            checkAutoComplete();

        });

        $('#'+inviteeInputId).focus();

    }

    function getMeetingById( meetingId ){


        for( var i=0; i < meetings.length; i++ ){

            var meeting = meetings[i];

            if( meeting.id == currentMeetingId ){
                return meeting;
            }

        }

        return null;

    }

    function getMeetingContactsById( meetingId ){

        if(uneditedInvitees[currentMeetingId] != null) meetingContactsArray = uneditedInvitees[currentMeetingId];

        for( var i=0; i < meetingContactsArray.length; i++ ){
            var meetingContacts = meetingContactsArray[i];

            if( meetingContacts.meetingId == meetingId ){
                return meetingContacts.contacts;
            }

        }

        return null;

    }

    function addMeetingContactsById( meetingId, contacts ){

        var meetingContacts = getMeetingContactsById( meetingId );

        if( meetingContacts == null ){
            meetingContactsArray.push( { meetingId:meetingId, contacts:contacts })
        } else {
            meetingContacts.contacts = contacts;
        }


    }

    function removeMeetingContactsById( meetingId )
    {

        for( var i=0; i < meetingContactsArray.length; i++ ){
            var meetingContacts = meetingContactsArray[i];

            if( meetingContacts.meetingId == meetingId ){
                meetingContactsArray.splice( i, 1 );
                break;
            }

        }
    }

    function removeMeetingById( meetingId ){
        for( var i=0; i < meetings.length; i++ ){

            var meeting = meetings[i];

            if( meeting.id == currentMeetingId ){
                meetings.splice( i, 1 );
                break;
            }

        }
    }

    function enterMeetingById( meetingId ){

        var meeting = getMeetingById( meetingId );

        console.log(" enterMeetingById  = "+meetingId)

        enterMeetingByKey( meeting.ukey );
    }

    function enterMeetingByKey( key ){

        console.log("ENTER MEETINg BY KEY = "+key);

        window.open( "/promote/presenter/session/"+key, "_blank", "width=1024, height=768, top:0, left:0");

    }

    function autoCompleteSelect( direction ){

        var autoCompleteItemLength = $(".llcontact-autocomplete-item").length;

        $(".llcontact-autocomplete-item").removeClass("llcontact-autocomplete-item-selected");
        $(".llcontact-autocomplete-item").addClass("llcontact-autocomplete-item-unselected");



        if( autoCompleteItemLength > 0 ){

            if( direction == "up" ){
                autoCompleteSelectIndex --;
                if( autoCompleteSelectIndex < -1 ){
                    autoCompleteSelectIndex = -1;
                }
            } else {
                autoCompleteSelectIndex++;

                if( autoCompleteSelectIndex > autoCompleteItemLength - 1 ){
                    autoCompleteSelectIndex = autoCompleteItemLength - 1;
                }
            }

            if( autoCompleteSelectIndex > -1){
                var autoCompleteItem = $(".llcontact-autocomplete-item:eq("+autoCompleteSelectIndex+")");

                autoCompleteItem.addClass("llcontact-autocomplete-item-selected");
                autoCompleteItem.removeClass("llcontact-autocomplete-item-unselected");
            }


            $(".llcontact-autocomplete-stage").stop( true, true ).animate({
                        scrollTop: autoCompleteItem.position().top
                    }, 200);

            //$(".llcontact-autocomplete").scrollTop( autoCompleteItem.position().top );
        }

    }

    function checkAutoComplete(){

        var inputValue = $('#'+inviteeInputId).val().toLowerCase();

        if( inputValue.length > 0){

            var position = llDomUtil.getGlobalPositionOfElement( $('#'+inviteeInputId) );

            position.y += 24;

            var contactsFound = [];


            for( var i=0; i < contacts.length;  i++ ){

                var contact = contacts[i];

                var firstNameIndexOf = -1;

                if( contact.firstname != null ){
                    firstNameIndexOf = contact.firstname.toLowerCase().indexOf( inputValue );
                }


                var surnameIndexOf = -1

                if( contact.surname  != null ){
                    surnameIndexOf = contact.surname.toLowerCase().indexOf( inputValue );
                }

                var emailIndexOf = -1

                if( contact.email  != null ){
                    emailIndexOf = contact.email.toLowerCase().indexOf( inputValue );
                }

                var indexOfs = [firstNameIndexOf, surnameIndexOf, emailIndexOf];

                //console.log(" firstNameIndexOf = "+firstNameIndexOf+"  lastNameIndexOf = "+surnameIndexOf+"  emailIndexOf = "+emailIndexOf);

                var bestIndexOf = 999999;

                var nameToUse = "";

                var email = "";

                for( var ii=0; ii < indexOfs.length; ii++ ){

                    var indexOf = indexOfs[ii];

                    if( indexOf != -1 && indexOf < bestIndexOf ){
                        bestIndexOf = indexOf;

                        if( ii < 2 ){
                            nameToUse = contact.firstname + " " + contact.surname;

                            email = contact.email;
                        } else {
                            nameToUse = contact.email;
                        }
                    }

                }

                if( bestIndexOf != 999999 ){

                    var indexOfName = nameToUse.toLowerCase().indexOf( inputValue.toLowerCase() );

                    var part = nameToUse.substr(indexOfName, inputValue.length );

                    var nameWithFormattingParts = nameToUse.split("");

                    console.log("  part = "+part);

                    nameWithFormattingParts.splice(indexOfName, inputValue.length, "<b>"+part+"</b>" );

                    var nameWithFormatting =  nameWithFormattingParts.join("");

                    var contactFound = {
                        indexOf:bestIndexOf,
                        contact:contact,
                        name:nameWithFormatting,
                        email:email
                    }

                    contactsFound.push( contactFound );
                }

            }

            contactsFound.sort( function( a, b ){

                return a.indexOf-b.indexOf;
            });

            if( contactsFound.length > 0 ){

                var list = "";

                for( var i=0; i < contactsFound.length; i++ ){

                    var contactFound = contactsFound[i];

                    var nameLength = contactFound.name.length;
                    var emailLength = contactFound.email.length;
                    var name = contactFound.name;
                    var email = contactFound.email;

                    if(nameLength > 20 || emailLength > 20)
                    {
                        if(email != '')  {
                            name = name.substr(0, 20) + '...';
                        }
                        if(email != '' && emailLength > 20)  {
                            email = email.substr(0, 20) + '...';
                        }
                    }

                    list += "<div id='llcontact-autocomplete-item-id-"+contactFound.contact.id+"' class='llcontact-autocomplete-item llcontact-autocomplete-item-unselected'>"+name+"<span style='color:#999; font-size:9pt;'> &nbsp;&nbsp;"+email+"</span></div>";

                }

                //max-height:200px; 

                $(".llcontact-autocomplete").html('<div class="llcontact-autocomplete-stage" style="position:absolute; left:'+position.x+'px; top:'+position.y+'px; width:260px; word-wrap:normal; background-color:#fff; z-index:9999; box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.4); overflow-y:auto; overflow-x:hidden;">'+list+'</div>');

                $(".llcontact-autocomplete-item").mousedown( this, function( event ){

                    var id = $(event.target).attr("id");

                    if( typeof id == "undefined"){
                        id = $(event.target).parent().attr('id');
                    }

                    id = id.replace("llcontact-autocomplete-item-id-", "");

                    addContactById( id );

                    hideAutoComplete();

                });

                autoCompleteSelectIndex = -1;

            } else {
                hideAutoComplete();
            }



        } else {
            hideAutoComplete();
        }




    }

    function hideAutoComplete()
    {
        $(".llcontact-autocomplete").html('');
        autoCompleteSelectIndex = -1;
    }

    function updateInviteeInput()
    {
        var inviteeInputString = $('#'+inviteeInputId).val();

        var invitesToAdd = [];

        var inviteeInputParts = inviteeInputString.split(/[,;|]+/);

        for( var i=0; i < inviteeInputParts.length; i++ ){

            var inviteeInputPart = inviteeInputParts[i];

            //remove spaces at beginning and end
            inviteeInputPart = inviteeInputPart.replace(/(^\s+|\s+$)/g,' ');

            var inviteeAdded = false;

            for( var ii = 0; ii < contacts.length; ii++ ){

                var contact = contacts[ii];

                var name = contact.firstname + " " + contact.surname;

                if( name.toLowerCase() == inviteeInputPart.toLowerCase() || contact.email.toLowerCase() == inviteeInputPart.toLowerCase() ){
                    addContactById( contact.id );
                    inviteeAdded = true;
                    break;
                }

            }

            if( !inviteeAdded ){
                addContactByEmail( inviteeInputPart );
            }


        }


    }

    function removeInvitee(){

        if( $('#'+inviteeInputId).val() == "" && invitees.length > 0 ){

            if( $(".emailBubble:last").hasClass("emailBubbleSelected") ){
                removeContactById( invitees[invitees.length - 1].id );
            } else {
                $(".emailBubble:last").addClass("emailBubbleSelected");
            }

        }
    }

    function setupMeetingList()
    {

        var meetingHtml = "";

        if( meetings.length > 0 ){
            meetingHtml += '<div style="">';
            meetingHtml += '<div style="background-color:#eee; height:50px; padding-top:10px; padding-left:20px; font-size:16pt;">Scheduled Meetings</div>';

            var currentMonth = '';
            var currentYear = '';

            for (var i = 0; i < meetings.length;  i++ ) {

                var meeting = meetings[i];



                var startDate = new Date( parseInt(meeting.start_datetime) );

                if( meeting.start_datetime != null && !isNaN( new Date( parseInt(meeting.start_datetime) ).getTime()) ){
                    startDate = new Date( parseInt(meeting.start_datetime) );
                }

                var yearOfMeeting = startDate.getFullYear();//date( 'Y', strtotime( startDate ) );

                if( yearOfMeeting != currentYear){
                    //meetingHtml += '<div style="background-color:#eee; height:30px; padding-top:3px; padding-left:20px; font-size:12pt;">'.yearOfMeeting.'</div>';
                    currentMonth = '';
                    currentYear = yearOfMeeting;
                }

                var monthOfMeeting = llFormatUtil.getMonthsShortVersionArray()[startDate.getMonth()];//date( 'F', strtotime( startDate ) );

                if( monthOfMeeting != currentMonth){
                    meetingHtml += '<div style="background-color:#eee; height:30px; padding-top:3px; padding-left:20px; font-size:12pt;">'+monthOfMeeting+'<span style="padding-left:10px; font-size:10pt; color:#aaaaaa;">'+yearOfMeeting+'</span></div>';
                    currentMonth = monthOfMeeting;
                }

                meetingHtml += '<div id="meeting-panel-'+meeting.id+'" class="row meeting-info-panel" style="word-wrap: normal; margin-left: 0 !important; margin-right: 0 !important; max-height:60px; border-left:solid #eee 2px; border-right:solid #eee 2px; border-bottom:solid #eee 2px; overflow:hidden;">';
                meetingHtml += '<div class="ion-arrow-right-b meeting-toggle llicon-button col-xs-1" style="font-size:10pt; width:40px; height:55px; text-align:center; padding-top:19px; cursor:pointer;"></div>';
                meetingHtml += '<div class="meeting-edit-button col-xs-7" style="float:left; cursor:pointer;"><div id="meeting-title" style="height: 35px; overflow: hidden;font-size:14pt; padding-top:6px;">'+meeting.subject+'</div><div style="font-size:10pt; padding-top:0px; color:#666;">'+llFormatUtil.getDateToStringLongUTCFormat(startDate)+'</div></div>';
                meetingHtml += '<div  class="ion-close meeting-delete-button llicon-button" style="float:right; height:60px; font-size:16pt; padding-left:10px; padding-right:15px; padding-top:14px; cursor:pointer;"></div>';
                //meetingHtml += '<div  class="ion-edit meeting-edit-button llicon-button" style="float:right; height:60px; font-size:16pt; padding-left:10px; padding-right:10px; padding-top:14px; cursor:pointer;"></div>';

                meetingHtml += '<div  class="col-xs-3 meeting-play-button llicon-link" style="height:60px; font-size:12pt; line-height: 55px; cursor:pointer; overflow: hidden;">Enter Meeting</div>';

                /*
                meetingHtml += '<div  class="ion-play meeting-play-button llicon-button" style="float:right; height:60px; font-size:16pt; padding-left:10px; padding-right:15px; padding-top:14px; cursor:pointer;"></div>';
                */
                meetingHtml += '<div style="clear:both; margin-left:60px;"><div style="font-size:14pt; padding-top:6px; padding-bottom:10px;">Invited Contacts</div>';

                meetingHtml += '<div id="meeting-panel-invitees-'+meeting.id+'"></div>'

                meetingHtml += '<div style="height:20px;"></div>';

                meetingHtml += '</div>';
                meetingHtml += '</div>';
            }
        } else {
            meetingHtml += '<div style="">You have no meetings scheduled.';
            meetingHtml += '</div>';
        }



        $("#meeting-stage").html( meetingHtml );

        setupMeetingListeners();

    }

    function setupMeetingContacts( meetingId ){

        var meetingInviteePanel = $("#meeting-panel-invitees-"+meetingId);

        var meetingContacts = getMeetingContactsById( meetingId );

        var meetingHtml = '';

        if( meetingContacts == null || meetingContacts.length == 0 ){
            meetingHtml += '<div>None</div>';
        } else {

            for( var ii = 0; ii < meetingContacts.length; ii++ ){

                var contact = meetingContacts[ii];

                var contactName = "";

                if( contact.firstname == null || contact.firstname == ""){
                    contactName = contact.email;
                } else {
                    contactName = contact.firstname + " " +contact.surname;
                }

                meetingHtml += '<div>'+contactName;

                /*
                if( contact.status == "accepted"){
                    meetingHtml += '<span style="color:#00aa00; padding-left:20px;">Accepted</span>';
                } else if( contact.status == "declined" ){
                    meetingHtml += '<span style="color:#aa0000; padding-left:20px;">Declined</span>';
                } else {
                    meetingHtml += '<span style="color:#aaa; padding-left:20px;">Undecided</span>';
                }
                */
                meetingHtml += '</div>';
            }
        }

        meetingInviteePanel.html( meetingHtml );
    }



    llCalendar.setDateCallback = function( date ){
        var nowDate = new Date();

        if( date.getTime() <= nowDate.getTime() ){
            $("#start-date-input").val( "Now" );

            if( currentMeetingId == null ){
                $('#form-commit-button').text('Start');
            }



        } else {

            var formattedDate = llFormatUtil.getDateToStringLongUTCFormat( date );

            $("#start-date-input").val( formattedDate );

            if( currentMeetingId == null ){
                $('#form-commit-button').text('Schedule');
            }
        }

        meetingStartDate = date;


    }



    llContactPicker.setContactCallback = function( contactId ){

        addContactById( contactId );


    }

    getData();


    setupListeners();


});