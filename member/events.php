<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_SESSION['userId'];?>";

    document.getElementById("nav-events").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Upcoming Events";
    document.getElementById("add-item-button").style.display = "none";

    const wrapper = document.getElementById("table-wrapper");

    wrapper.innerHTML = `<div id="search-wrapper" class="row">
                            <div class="col-4">
                                    <form onsubmit="return false;">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search Keyword" id="search-input">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit" id="search-button">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="matches-list-wrapper">
                            <div class="row" id="matches-card-container"></div>
                        </div>`;

    var searchInput = document.getElementById("search-input");

    document.getElementById("search-button").addEventListener("click", () => {
        fetchData(searchInput.value);
    });

    const fetchData = async (searchWord) => {
        var url = "";

        if(searchWord == ""){
            url = getURL("events/read_upcoming_events.php");
        }else{
            url = getURL("events/search_upcoming_events.php?query=" + searchWord);
        }

        var events = await fetch(url);
        events = await events.json();
        events = events.records;

        var memberEvents = await fetch(getURL("members_events/read_member_events.php?id=" + memberId));
        memberEvents = await memberEvents.json();
        memberEvents = memberEvents.records;

        var filteredEvents = [];

        for(var i = 0; i < events.length; i++){
            var isAttending = false;

            for(var j = 0; j < memberEvents.length; j++){
                if(events[i].id == memberEvents[j].eventId){
                    isAttending = true;
                    break;
                }
            }

            if(!isAttending){
                filteredEvents.push(events[i]);
            }
        }

        var container = document.getElementById("matches-card-container");
        container.innerHTML = "";

        if(filteredEvents.length < 1){
            container.innerHTML = `<h3 class="ml-3">No upcoming events found</h3>`;
        }else{
            displayEvents(filteredEvents);
        }

    }

    fetchData("");
</script>

<!-- functions for displaying all the upcoming events -->
<script>
    const displayEvents = (events) => {
        var cardContainer = document.getElementById("matches-card-container");
        cardContainer.innerHTML = "";

        events.forEach(event => {
        
            cardContainer.innerHTML += `
            <div class="col-5 pb-3 mr-5">

                <div class="card" style="width: 30rem;">
                    <div class="card-image-container" style="height: 250px;">
                        <div class="overall-percentage">$${event.fee}</div>
                        <img class="card-img-top" src="../resources/images/uploads/event/${event.image}" alt="Card image cap">
                    </div>

                    <div class="card-body">
                    <h5 class="card-title mb-0">${event.name}</h5>
                    <p>${event.description}</p>

                    <button class="btn btn-primary btn-sm send-request-btn" onclick="attendEvent('${event.id}');">Attend Event</button>
                    <button class="btn btn-secondary btn-sm btn-view-match" onclick="triggerViewModal('${event.id}');">View</button>
                    </div>
                </div>
            </div>`;          
        });
    }   
</script>

<!-- functions for viewing record details -->
<script>
    const triggerViewModal = async (id) => {
        try{
            var result = await fetch(getURL("events/read_one.php?id=" + id));
            result = await result.json();
            
            if(result.succ){
                viewRecordDetails(result.record);
            }else{
                toastr.error(result.msg, "Error");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error");
        }
    }

    const viewRecordDetails = (record) => {
        document.querySelector('.modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" onclick="attendEvent('${record.id}');" class="btn btn-primary">Attend Event</button>`;

        document.querySelector('#modalLongTitle').innerHTML = "Event Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../resources/images/uploads/event/${record.image}" alt="">
                </div>
                <div class="col-6">
                    <label class="view-control">Name: <strong class="short-text">${record.name}</strong></label>
                    <label class="view-control">Category: <strong class="short-text">${record.categoryName}</strong></label>
                    <label class="view-control">Start DateTime: <strong class="short-text">${dateTimeChange(record.startDateTime)}</strong></label>
                    <label class="view-control">End DateTime: <strong class="short-text">${dateTimeChange(record.endDateTime)}</strong></label>
                    <label class="view-control">Event Fee: <strong class="short-text">Rs ${parseFloat(record.fee)}</strong></label>
                    <label class="view-control">Status: <strong class="short-text">${getEventStatusStyled(record.status)}</strong></label>
                    <label class="view-control">Created Date: <strong class="short-text">${record.created_at}</strong></label>
                    <label class="view-control">Location: <strong class="long-text">${record.location}</strong></label>
                    <label class="view-control">Description: <strong class="long-text">${record.description}</strong></label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }
</script>

<!-- functions for attending an event -->
<script>
    const attendEvent = async (eventId) => {
        var formdata = {
            'memberId':memberId,
            'eventId':eventId,
        };

        try{
            var result = await fetch(getURL("members_events/create.php"), { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formdata),
            });

            result = await result.json();

            if(result.succ){
                $('#ModalCenter').modal('hide');
                toastr.success(result.msg, "Sucess!");
                fetchData("");
            }else{
                toastr.error(result.msg, "Error!");
            }
            
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }
    }

</script>