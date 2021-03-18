<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    var currentRowMenu = 0;

    document.getElementById("nav-users").classList.add("active-page");
    document.getElementById("add-item-button").textContent = "Add User";
    document.getElementById("pageTitle").textContent = "Users";

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Name</th>
            <th>Email</th>
            <th class="td-center">Mobile No</th>
            <th>Role</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("staffs/read_all.php"),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "name" },
            { "data": "email"},
            { "data": "mobileNo", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "role", "render": function (data) {return `<div>${getUserRole(data)}</div>`}},
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
            "emptyTable": "No users found"
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
        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span style="color:red;">*</span></label>
                        <input type="text" id="name" class="form-control" placeholder="Staff Name" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nic">NIC <span style="color:red;">*</span></label>
                        <input type="text" id="nic" class="form-control" placeholder="Staff NIC" required="required">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email <span style="color:red;">*</span></label>
                        <input type="email" id="email" class="form-control" placeholder="Staff Email" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobileNo">Mobile No <span style="color:red;">*</span></label>
                        <input type="number" id="mobileNo" class="form-control" placeholder="Staff Mobile No" required="required">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Role <span style="color:red;">*</span></label>
                        <select name="role" id="role" class="form-control" required="required">
                            <option selected disabled value="">Select user role</option>
                            <option value="0">Admin</option>
                            <option value="1">Receptionist</option>
                            <option value="2">Client Service Agent</option>
                            <option value="3">Senior Client Service Agent</option>
                            <option value="4">Finance Manager</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address <span style="color:red;">*</span></label>
                        <input type="text" id="address" class="form-control" placeholder="Staff Address" required="required">
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
            'NIC':document.getElementById('nic').value.trim(),
            'email':document.getElementById('email').value.trim(),
            'mobileNo':document.getElementById('mobileNo').value.trim(),
            'role':document.getElementById('role').value,
            'address':document.getElementById('address').value
        };

        console.log(formdata);

        try{
            var result = await fetch(getURL("staffs/create.php"), { 
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
            var result = await fetch(getURL("staffs/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "Update User";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="updateRecordBtn" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="return false;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span style="color:red;">*</span></label>
                        <input type="text" id="name" class="form-control" value="${record.name}" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nic">NIC <span style="color:red;">*</span></label>
                        <input type="text" id="nic" class="form-control" value="${record.NIC}" required="required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email <span style="color:red;">*</span></label>
                        <input type="email" id="email" class="form-control" value="${record.email}" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobileNo">Mobile No <span style="color:red;">*</span></label>
                        <input type="number" id="mobileNo" class="form-control" value="${record.mobileNo}" required="required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Role <span style="color:red;">*</span></label>
                        <select id="role" class="form-control" required="required">
                            <option value="0">Admin</option>
                            <option value="1">Receptionist</option>
                            <option value="2">Client Service Agent</option>
                            <option value="3">Senior Client Service Agent</option>
                            <option value="4">Finance Manager</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
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
                        <label for="address">Address <span style="color:red;">*</span></label>
                        <input type="text" id="address" class="form-control" value="${record.address}" required="required">
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        document.getElementById("role").value = record.role;
        document.getElementById("status").value = record.status;
        
        $('#ModalCenter').modal('toggle');

        document.getElementById('updateRecordBtn').addEventListener('click', async () => {
            showLoader("updateRecordBtn", "Submitting...");

            var formdata = {
                'id': record.id,
                'name':document.getElementById('name').value.trim(),
                'NIC':document.getElementById('nic').value.trim(),
                'email':document.getElementById('email').value.trim(),
                'mobileNo':document.getElementById('mobileNo').value.trim(),
                'address':document.getElementById('address').value,
                'role':document.getElementById('role').value,
                'status':document.getElementById('status').value,
            };

            try{
                var result = await fetch(getURL("staffs/update.php"), { 
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
        showLoaderOnViewPort("Removing user...");

        try{
            var result = await fetch(getURL("staffs/remove.php?id=" + id));
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
            var result = await fetch(getURL("staffs/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "User Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../../resources/images/uploads/profile/${record.image}" alt="">
                </div>
                <div class="col-6">
                    <label class="view-control">Name: <strong class="short-text">${record.name}</strong></label>
                    <label class="view-control">NIC: <strong class="short-text">${record.NIC}</strong></label>
                    <label class="view-control">Email: <strong class="short-text">${record.email}</strong></label>
                    <label class="view-control">Mobile No: <strong class="short-text">${record.mobileNo}</strong></label>
                    <label class="view-control">Role: <strong class="short-text">${getUserRole(record.role)}</strong></label>
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