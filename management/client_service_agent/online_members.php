<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    var currentRowMenu = 0;

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";
    document.getElementById("pageTitle").textContent = "Online Members";

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Name</th>
            <th>NIC</th>
            <th>Email</th>
            <th class="td-center">Mobile No</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("members/read_all_online_members.php"),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "name" },
            { "data": "NIC" },
            { "data": "email"},
            { "data": "mobileNo", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "status", "render": function (data) {return getStatusStyled(data)}},
            {
                "data": "id",
                "render": function (data) {
                    return `
                        <div class="td-right">
                            <button id="record-button-${data}" onclick="showRecordMenu('${data}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data}')"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    <a href="member_events.php?id=${data}"><i class="fas fa-ticket-alt"></i>Events</a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No members found"
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
            var result = await fetch(getURL("members/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "Member Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../../resources/images/uploads/profile/${record.image}" alt="">
                </div>
                <div class="col-6">
                    <label class="view-control">Name: <strong class="short-text">${record.name}</strong></label>
                    <label class="view-control">NIC: <strong class="short-text">${record.NIC}</strong></label>
                    <label class="view-control">Gender: <strong class="short-text">${record.gender}</strong></label>
                    <label class="view-control">Email: <strong class="short-text">${record.email}</strong></label>
                    <label class="view-control">Mobile No: <strong class="short-text">${record.mobileNo}</strong></label>
                    <label class="view-control">Status: <strong class="short-text">${getStatusStyled(record.status)}</strong></label>
                    <label class="view-control">Created Date: <strong class="short-text">${record.created_at}</strong></label>
                    <label class="view-control">Address: <strong class="long-text">${record.address}</strong></label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }
</script>