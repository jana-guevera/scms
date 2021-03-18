<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    var currentRowMenu = 0;

    document.getElementById("nav-events").classList.add("active-page");
    document.getElementById("add-item-button").textContent = "Add Event";
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
                    var updateModal = data.status == 1 ? ` <a href="#" onclick="triggerUpdateModal('${data.id}')"><i class="fas fa-edit yellow"></i>Update</a>` 
                                                        : "";

                    var cancelModal = data.status == 1 ? `<a href="#" onclick="triggerCancelModal('${data.id}')"><i class="fas fa-minus-circle red"></i>Cancel Event</a>`
                                                        : "";
                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.id}')"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    ${updateModal}
                                    <a href="#" onclick="triggerRemoveModal('${data.id}')"><i class="fas fa-trash-alt red"></i>Remove</a>
                                    ${cancelModal}
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

<!-- functions for adding record -->
<script>
    const triggerAddNew = async () => {
        try{
            var result = await fetch(getURL("event_categories/read_all.php"));
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

    const buildAddModal = (categories) => {
        document.querySelector('#modalLongTitle').innerHTML = "Add Event";

        var selectionList = `<option selected disabled value="">Select category</option>`;

        categories.forEach(cat => {
            selectionList += `<option value="${cat.id}">${cat.name}</option>`;
        });

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="eventCategory">Event Category <span style="color:red;">*</span></label>
                        <select id="eventCategory" class="form-control" required="required">
                            ${selectionList}
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span style="color:red;">*</span></label>
                        <input type="text" id="name" class="form-control" placeholder="Event Name" required="required">
                    </div>
                </div>
            </div>

            <div class="row">
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

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="location">Location <span style="color:red;">*</span></label>
                        <input type="text" id="location" class="form-control" placeholder="Event Location" required="required">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="description" rows="2" placeholder="Event Description"></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="eventFee">Event Fee <span style="color:red;">*</span></label>
                        <input type="number" id="eventFee" class="form-control" placeholder="Event Fee" required="required">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="profileImage">Profile Image</label>
                        <input type="file" id="profileImage" class="form-control-file">
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }

    const addRecord = async () => {
        showLoader("addRecordBtn", "Submitting...");
        const imageFile = document.querySelector("#profileImage");

        var formdata = {
            'name':document.getElementById('name').value.trim(),
            'startDateTime':document.getElementById('startDateTime').value.trim(),
            'endDateTime':document.getElementById('endDateTime').value.trim(),
            'categoryId':document.getElementById('eventCategory').value.trim(),
            'location':document.getElementById('location').value.trim(),
            'fee':document.getElementById('eventFee').value.trim(),
            'description':document.getElementById('description').value.trim(),
            'image': null
        };

        console.log(formdata);

        try{

            if (imageFile.value.length != 0) {
                const result = await uploadImage(imageFile);

                if (result.error) {
                    alertError(result.error);
                    return;
                }

                formdata.image = result.imageName;
            }

            var result = await fetch(getURL("events/create.php"), { 
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

    const uploadImage = async (fileInput) => {
        const formdata = new FormData();
        formdata.append('image', fileInput.files[0]);

        var result = await fetch(getURL("storage/upload_event_image.php"), {
            method: 'POST',
            body: formdata
        });

        result = await result.json();
        return result;
    }
</script>

<!-- functions for updating record -->
<script>
    const triggerUpdateModal = async(id) => {
        try{
            var result = await fetch(getURL("events/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "Update Event";

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
                        <label for="eventFee">Event Fee <span style="color:red;">*</span></label>
                        <input type="number" id="eventFee" class="form-control" value="${record.fee}" required="required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="startDateTime">Start DateTime<span style="color:red;">*</span></label>
                        <input type="datetime-local" id="startDateTime" class="form-control" value="${record.startDateTime}" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="endDateTime">End DateTime<span style="color:red;">*</span></label>
                        <input type="datetime-local" id="endDateTime"  value="${record.endDateTime}" class="form-control" required="required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="location">Location <span style="color:red;">*</span></label>
                        <input type="text" id="location" class="form-control" value="${record.location}" required="required">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="description" rows="2">${record.description}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="profileImage">Profile Image</label>
                        <input type="file" id="profileImage" class="form-control-file">
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');

        document.getElementById('updateRecordBtn').addEventListener('click', async () => {
            showLoader("updateRecordBtn", "Submitting...");
            const imageFile = document.querySelector("#profileImage");

            var formdata = {
                'id': record.id,
                'name':document.getElementById('name').value.trim(),
                'startDateTime':document.getElementById('startDateTime').value.trim(),
                'endDateTime':document.getElementById('endDateTime').value.trim(),
                'location':document.getElementById('location').value.trim(),
                'fee':document.getElementById('eventFee').value.trim(),
                'description':document.getElementById('description').value.trim(),
                'image': record.image,
                'oldImage': record.image,
            };

            try{

                if (imageFile.value.length != 0) {
                    const result = await uploadImage(imageFile);

                    if (result.error) {
                        alertError(result.error);
                        return;
                    }

                    formdata.image = result.imageName;
                }

                var result = await fetch(getURL("events/update.php"), { 
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
        showLoaderOnViewPort("Removing event...");

        try{
            var result = await fetch(getURL("events/remove.php?id=" + id));
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
            text: "Once cancelled, you will not be able to activate the event again!",
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
            var result = await fetch(getURL("events/cancel.php?id=" + id));
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