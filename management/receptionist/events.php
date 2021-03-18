<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    var currentRowMenu = 0;

    document.getElementById("nav-events").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";
    document.getElementById("pageTitle").textContent = "Events";

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
            "url": getURL("events/read_all.php"),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "name" },
            { "data": "categoryName" },
            { "data": "startDateTime", "render": function (data) { return dateTimeChange(data); }},
            { "data": "endDateTime", "render": function (data) { return dateTimeChange(data); }},   
            { "data": "fee", "render": function (data) { return parseFloat(data) }},
            { "data": "status", "render": function (data) {return getEventStatusStyled(data)}},
            {
                "data": {"id": 'id', "status":'status'},
                "render": function (data) {
                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.id}')"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    <a href="attending_members.php?id=${data.id}"><i class="fas fa-poll"></i>Attending Members</a>
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