<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    document.getElementById("nav-event_categories").classList.add("active-page");
    document.getElementById("add-item-button").textContent = "Add Category";
    document.getElementById("pageTitle").textContent = "Event Categories";
    document.querySelector('#model-wrapper').classList.remove("modal-lg");

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Name</th>
            <th>Description</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("event_categories/read_all.php"),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "name" },
            { "data": "description"},
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
                                    <a href="#" onclick="triggerUpdateModal('${data}')"><i class="fas fa-edit yellow"></i>Update</a>
                                    <a href="#" onclick="triggerRemoveModal('${data}')"><i class="fas fa-trash-alt red"></i>Remove</a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No categories found"
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
    const triggerAddNew = () => {
        document.querySelector('#modalLongTitle').innerHTML = "Add Event Category";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">Name <span style="color:red;">*</span></label>
                        <input type="text" id="name" class="form-control" placeholder="Category Name" required="required">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Category Description"></textarea>
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
            'name':document.getElementById('name').value.trim(),
            'description':document.getElementById('description').value.trim(),
        };

        try{
            var result = await fetch(getURL("event_categories/create.php"), { 
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

<!-- functions for updating record -->
<script>
    const triggerUpdateModal = async(id) => {
        try{
            var result = await fetch(getURL("event_categories/read_one.php?id=" + id));
            result = await result.json();
            
            if(result.succ){
                updateRecord(result.record);
            }else{
                toastr.error(result.msg, "Error");
            }
        }catch(error){
            console.error(error);
            toastr.error("Something went wrong. Please try again!", "Error");
        }
    }

    const updateRecord = async (record) => {
        document.querySelector('#modalLongTitle').innerHTML = "Update Event Category";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="updateRecordBtn" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">Name <span style="color:red;">*</span></label>
                        <input type="text" id="name" class="form-control" value="${record.name}" required="required">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="role">Status <span style="color:red;">*</span></label>
                        <select name="status" id="status" class="form-control" required="required">
                            <option value="1">Active</option>
                            <option value="0">Blocked</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="description" rows="3">${record.description}</textarea>
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        document.getElementById("status").value = record.status;
        
        $('#ModalCenter').modal('toggle');

        document.getElementById('updateRecordBtn').addEventListener('click', async () => {
            showLoader("updateRecordBtn", "Submitting...");

            var formdata = {
                'id': record.id,
                'name':document.getElementById('name').value.trim(),
                'description':document.getElementById('description').value.trim(),
                'status':document.getElementById('status').value,
            };

            try{
                var result = await fetch(getURL("event_categories/update.php"), { 
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
                    console.error(result.error);
                }
                
            }catch(error){
                console.error(error);
                toastr.error("Something went wrong. Please try again!", "Error!");
            }finally{
                hideLoader("updateRecordBtn", "Save Changes");
            }
        });
    }
</script>

<!-- functions for removing records -->
<script>
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
        showLoaderOnViewPort("Removing event category...");

        try{
            var result = await fetch(getURL("event_categories/remove.php?id=" + id));
            result = await result.json();

            if(result.succ){
                toastr.success(result.msg, "Success!");
                rePopulateTable();
            }else{
                toastr.error(result.msg, "Error!");
                console.error(result.error);
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
            var result = await fetch(getURL("event_categories/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "Survey Category Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-12">
                    <label class="view-control">Name: <strong class="short-text">${record.name}</strong></label>
                    <label class="view-control">Status: <strong class="short-text">${getStatusStyled(record.status)}</strong></label>
                    <label class="view-control">Created Date: <strong class="short-text">${record.created_at}</strong></label>
                    <label class="view-control">Description: <strong class="long-text">${record.description}</strong></label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }
</script>