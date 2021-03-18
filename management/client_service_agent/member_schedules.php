<?php include "./layout/layout.php"; ?>

<script src="../../resources/js/jspdf.min.js"></script>
<script src="../../resources/js/jspdf.plugin.autotable.js"></script>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";
    var currentRowMenu = 0;

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Events";
    document.getElementById("add-item-button").textContent = "Add Schedule";
    document.getElementById("add-button-wrapper").innerHTML += `
        <button class="btn btn-primary mb-4" id="schedule-report-btn" type="button" onclick="triggerScheduleReportModal()">
           Schedule Report
        </button>
    `;

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="offline_members.php">Members</a></li>
                            <li><a href="member_schedules.php?id=${memberId}">Member Schedules</a></li>`;

    const fetchData = async () => {
        var result = await fetch(getURL("members/read_one.php?id=" + memberId));
        result = await result.json();

        document.getElementById("pageTitle").textContent = "Schedules -- " + result.record.name;
    }

    fetchData();

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Friend</th>
            <th>Start DateTime</th>
            <th>End DateTime</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("friends_schedules/read_member_schedules.php?id=" + memberId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "scheduleReceiver", "render": function (data) { return data.name; } },
            { "data": "startDateTime", "render": function (data) { return dateTimeChange(data); } },
            { "data": "endDateTime", "render": function (data) { return dateTimeChange(data); } }, 
            { "data": "status", "render": function (data) {return getMemberScheduleStatusTextStyled(data)}},
            {
                "data": {"id": 'id', "status":'status', "scheduleCreaterId": 'scheduleCreaterId'},
                "render": function (data) {
                    var html = "";

                    if(data.scheduleCreaterId == memberId){
                        if(data.status != 2 && data.status != 3){
                            html += `<a href="#" onclick="triggerCancelModal('${data.id}')"><i class="fas fa-minus-circle red"></i>Cancel Schedule</a>`;
                        }

                        html += `<a href="#" onclick="triggerRemoveModal('${data.id}')"><i class="fas fa-trash-alt red"></i>Remove</a>`;
                    }else{

                        if(data.status == 0){
                            html += `<a href="#" onclick="triggerDeclineModal('${data.id}')"><i class="fas fa-minus-circle red"></i>Decline</a>
                                <a href="#" onclick="confirmRequest('${data.id}')"><i class="fas fa-check-circle green"></i>Confirm</a>`;
                        }else if(data.status == 1){
                            html += `<a href="#" onclick="triggerCancelModal('${data.id}')"><i class="fas fa-minus-circle red"></i>Cancel Schedule</a>`;
                        }
                    }
                    
    
                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.id}')"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    ${html}
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
            var result = await fetch(getURL("matched_friends/get_member_friends.php?id=" + memberId));
            result = await result.json();
            if(result.records.length > 0){
                var friends = result.records.filter(e => e.status == 1);

                if(friends.length > 0){
                    buildAddModal(result.records);
                }else{
                    toastr.error("No friends have been found for scheduling!", "Error")
                }
            }else{
                toastr.error(result.msg, "Error");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again.", "Error");
        }
    }

    const buildAddModal = (friends) => {
        document.querySelector('#model-wrapper').classList.add("modal-lg");
        document.querySelector('#modalLongTitle').innerHTML = "Add Schedule";

        var selectionList = `<option selected disabled value="">Select Friend</option>`;

        friends.forEach(fr => {
            selectionList += `<option value="${fr.friend.id}">${fr.friend.name}</option>`;
        });

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="scheduleFriend">Friend <span style="color:red;">*</span></label>
                        <select id="scheduleFriend" class="form-control" required="required">
                            ${selectionList}
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="startDateTime">Start DateTime<span style="color:red;">*</span></label>
                        <input type="datetime-local" id="startDateTime" class="form-control" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="endDateTime">End DateTime<span style="color:red;">*</span></label>
                        <input type="datetime-local" id="endDateTime" class="form-control" required="required">
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
            'scheduleCreaterId': memberId,
            'scheduleReceiverId':document.getElementById('scheduleFriend').value.trim(),
            'startDateTime':document.getElementById('startDateTime').value.trim(),
            'endDateTime':document.getElementById('endDateTime').value.trim(),
        };

        try{
            var result = await fetch(getURL("friends_schedules/create.php"), { 
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
        }
    }
</script>     

<!-- functions for confirming, cancelling, declining and removing schedule  -->
<script>
    const confirmRequest = async (id) => {
        try{    
            var result = await fetch(getURL("friends_schedules/confirm_schedule.php?id=" + id));
            result = await result.json();

            if(result.succ){
                toastr.success(result.msg, "Sucess!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }

        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }
    }

    const triggerDeclineModal = async (id) => {
        try{    
            var result = await fetch(getURL("friends_schedules/decline_schedule.php?id=" + id));
            result = await result.json();

            if(result.succ){
                toastr.success(result.msg, "Sucess!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }

        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }
    }

    const triggerCancelModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once cancelled, you will not be able to activate the schedule again!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    cancelSchedule(id);
                }
            });
    }

    const cancelSchedule = async (id) => {
        try{    
            var result = await fetch(getURL("friends_schedules/cancel_schedule.php?id=" + id));
            result = await result.json();

            if(result.succ){
                toastr.success(result.msg, "Sucess!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
            }

        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error!");
        }
    }

    const triggerRemoveModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    deleteRecord(id);
                }
            });
    }

    const deleteRecord = async (id) => {
        showLoaderOnViewPort("Removing event...");

        try{
            var result = await fetch(getURL("friends_schedules/remove.php?id=" + id));
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


<!-- functions for viewing record details -->
<script>
    const triggerViewModal = async (id) => {
        try{
            var result = await fetch(getURL("friends_schedules/read_one.php?id=" + id));
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
        document.querySelector('#model-wrapper').classList.remove("modal-lg");
        document.querySelector('.modal-footer').innerHTML = "";
        document.querySelector('#modalLongTitle').innerHTML = "Schedule Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-12">
                    <label class="view-control">Friend: <strong class="short-text">${record.scheduleReceiver.name}</strong></label>
                    <label class="view-control">Requested By: <strong class="short-text">${record.scheduleCreator.name}</strong></label>
                    <label class="view-control">Start DateTime: <strong class="short-text">${dateTimeChange(record.startDateTime)}</strong></label>
                    <label class="view-control">End DateTime: <strong class="short-text">${dateTimeChange(record.endDateTime)}</strong></label>
                    <label class="view-control">Status: <strong class="short-text">${getMemberScheduleStatusTextStyled(record.status)}</strong></label>
                    <label class="view-control">Created Date: <strong class="short-text">${record.created_at}</strong></label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }
</script>

<!-- function for generating schedule report -->
<script>
    const triggerScheduleReportModal = () => {
        document.querySelector('#model-wrapper').classList.remove("modal-lg");

        document.querySelector('#modalLongTitle').innerHTML = "Daily Schedule Report";

        document.querySelector('.modal-footer').innerHTML = `
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="addRecordBtn" onclick="getReport();" class="btn btn-primary">Get Report</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="scheduleDate">Date<span style="color:red;">*</span></label>
                        <input type="date" id="scheduleDate" class="form-control" required="required">
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;

        $('#ModalCenter').modal('toggle');
    }

    const getReport = async () => {
        $('#ModalCenter').modal('toggle');

        var scheduleDate = document.getElementById("scheduleDate").value;

        var result = await fetch(getURL("friends_schedules/get_daily_schedules.php?id=" + memberId + "&date=" + scheduleDate));
        result = await result.json();

        if(result.records.length > 0){
            downloadReport(result.records, scheduleDate);
        }else{
            toastr.error("No schedule has been made for the selected date!", "Error");
        }     
    } 

    const downloadReport = (records, scheduleDate) => {
        var doc = new jsPDF('l', 'pt', 'a4');

        var pdf = [];

        records.forEach(record => {
            var temp = [
                record.scheduleCreator.name, 
                record.scheduleCreator.email, 
                record.scheduleReceiver.name,
                record.scheduleReceiver.email,
                dateTimeChange(record.startDateTime),
                dateTimeChange(record.endDateTime),
                getMemberScheduleStatusText(record.status),
            ]; 

            pdf.push(temp);
        });

        doc.text(40, 30, "Daily Schedules - " + scheduleDate);

        doc.autoTable({
            margin: { top: 50 },
            head: [
                ['Scheduled By', 'Email', 'Friend', 'Friend Email', "Start Time", "End Time", "Status"],
            ],
            body:pdf ,
        });

        doc.save(`daily_schedules.pdf`);
    }
</script>