<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";
    var currentRowMenu = 0;

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Events";
    document.getElementById("add-item-button").textContent = "Attent Event";

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="#" onClick="window.history.back();">Members</a></li>
                            <li><a href="member_events.php?id=${memberId}">Member Events</a></li>`;

    const fetchData = async () => {
        var result = await fetch(getURL("members/read_one.php?id=" + memberId));
        result = await result.json();

        document.getElementById("pageTitle").textContent = "Events -- " + result.record.name;

        if(result.record.memberType == 1){
            document.getElementById("add-item-button").style.display = "none";
        }
    }

    fetchData();

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Start DateTime</th>
            <th>End DateTime</th>
            <th>Event Fees (RS)</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("members_events/read_member_events.php?id=" + memberId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "event", "render": function (data) { return `<div class="td-center">${data.id}</div>` } },
            { "data": "event", "render": function (data) { return data.name; } },
            { "data": "event", "render": function (data) { return data.categoryName; } },
            { "data": "event", "render": function (data) { return dateTimeChange(data.startDateTime); } },
            { "data": "event", "render": function (data) { return dateTimeChange(data.endDateTime); } }, 
            { "data": "event", "render": function (data) { return data.fee; } },
            { "data": "status", "render": function (data) {return getMemberEventStatusStyled(data)}},
            {
                "data": {"id": 'id', "status":'status', "event": 'event'},
                "render": function (data) {
                    var cancelModal = "";

                    if(data.status == 0){
                        var cancelModal = `
                                <a href="#" onclick="triggerCancelModal('${data.id}')"><i class="fas fa-minus-circle red"></i>Cancel Event</a>
                                <a href="#" onclick="triggerAttendedModal('${data.id}')"><i class="fas fa-check green"></i>Attended</a>
                            `;
                    }
                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.event.id}')"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    ${cancelModal}
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No events were found"
        },
        "width": "100%"
    });

    // function to show menu for the record
    const showRecordMenu = (id) => {
        document.querySelector("#dropdown-wrapper-" + id).style.display = "block";
        
        currentRowMenu = id;

        window.addEventListener('click', removeRecordMenu);
    }

    // function to re-populate the table
    const rePopulateTable = () => {
        dataTable.ajax.reload();
    }

</script>

<!-- functions for adding record -->
<script>
    const triggerAddNew = async () => {
        try{
            var result = await fetch(getURL("events/read_upcoming_events.php"));
            result = await result.json();
            if(result.records.length > 0){
                buildAddModal(result.records);
            }else{
                toastr.error(result.msg, "Error");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again.", "Error");
        }
    }

    const buildAddModal = (events) => {
        document.querySelector('#model-wrapper').classList.remove("modal-lg");

        var selectionList = `<option selected disabled value="">Select an event</option>`;

        events.forEach(event => {
            selectionList += `<option value="${event.id}">${event.name}</option>`;
        });

        document.querySelector('#modalLongTitle').innerHTML = "Attent an Event";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="event">Event <span style="color:red;">*</span></label>
                        <select id="event" class="form-control" required="required">
                            ${selectionList}
                        </select>
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }

    const addRecord = async () => {
        showLoader("addRecordBtn", "Submitting...");

        var formdata = {
            'memberId':memberId,
            'eventId':document.getElementById('event').value.trim(),
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
                $('#ModalCenter').modal('toggle');
                toastr.success(result.msg, "Sucess!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }
            
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }finally{
            hideLoader("addRecordBtn", "Save Changes");
            document.querySelector('#model-wrapper').classList.add("modal-lg");
        }
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
        document.querySelector('.modal-footer').innerHTML = "";
        document.querySelector('#modalLongTitle').innerHTML = "Event Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../../resources/images/uploads/event/${record.image}" alt="">
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

<!-- function for cancelling an event -->
<script>
    const triggerCancelModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once cancelled, you will have to attent the event again.!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    cancelRecord(id);
                }
            });
    }

    const cancelRecord = async (id) => {
        showLoaderOnViewPort("Cancelling event...");

        try{
            var result = await fetch(getURL("members_events/remove.php?id=" + id));
            result = await result.json();
            
            if(result.succ){
                toastr.success(result.msg, "Success!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }finally{
            hideLoaderOnViewPort();
        }
    }
</script>

<!-- function for marking an event as attented -->
<script>
    const triggerAttendedModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once attended, the member will be charged for the event fee.!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    attendEvent(id);
                }
            });
    }

    const attendEvent = async (id) => {
        showLoaderOnViewPort("Attending event...");

        try{
            var result = await fetch(getURL("members_events/attend_event.php?id=" + id));
            result = await result.text();
            console.log(result);
            
            if(result.succ){
                toastr.success(result.msg, "Success!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }finally{
            hideLoaderOnViewPort();
        }
    }
</script>