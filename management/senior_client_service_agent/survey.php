<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    var currentRowMenu = 0;

    document.getElementById("nav-survey").classList.add("active-page");
    document.getElementById("add-item-button").textContent = "Add Survey";
    document.getElementById("pageTitle").textContent = "Survey";
    document.querySelector('#model-wrapper').classList.remove("modal-lg");

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Question</th>
            <th>Question Type</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("survey/read_all.php"),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "question" },
            { "data": "inputType", "render": function (data) { return getInputTypeText(data); } },
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
                                    <a href="#" onclick="triggerRemoveModal('${data}')"><i class="fas fa-trash-alt red"></i>Remove</a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No survey found"
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
    const triggerAddNew = async (id) => {
        try{
            var result = await fetch(getURL("survey_categories/read_all.php"));
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
        var selectionList = `<option selected disabled value="">Select category</option>`;

        categories.forEach(cat => {
            selectionList += `<option value="${cat.id}">${cat.name}</option>`;
        });
        document.querySelector('#modalLongTitle').innerHTML = "Add Survey";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord();" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="surveyCategory">Question Category <span style="color:red;">*</span></label>
                        <select id="surveyCategory" class="form-control" required="required">
                            ${selectionList}
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="question">Question <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="question" rows="2" placeholder="Question"></textarea>
                    </div>
                </div>
            </div>
           
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="answers">Answers <span style="color:red;">*</span></label>
                        <textarea class="form-control" id="answers" rows="2" placeholder="Answers seperated by commas"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="inputType">Question Type <span style="color:red;">*</span></label>
                        <select id="inputType" class="form-control" required="required">
                            <option selected disabled value="">Select question type</option>
                            <option value="0">Binary Question</option>
                            <option value="1">Multi-Answer Questions Question</option>
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
            'question':document.getElementById('question').value.trim(),
            'answers':document.getElementById('answers').value.trim(),
            'categoryId':document.getElementById('surveyCategory').value.trim(),
            'inputType':document.getElementById('inputType').value,
        };

        try{
            var result = await fetch(getURL("survey/create.php"), { 
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
            hideLoader("addRecordBtn", "Save Changes");
        }
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
        showLoaderOnViewPort("Removing survey...");

        try{
            var result = await fetch(getURL("survey/remove.php?id=" + id));
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
            var result = await fetch(getURL("survey/read_one.php?id=" + id));
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
        document.querySelector('#modalLongTitle').innerHTML = "Survey Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-12">
                    <label class="view-control">Question Category: <strong class="short-text">${record.categoryName}</strong></label>
                    <label class="view-control">Question Type: <strong class="short-text">${getInputTypeText(record.inputType)}</strong></label>
                    <label class="view-control">Created Date: <strong class="short-text">${record.created_at}</strong></label>
                    <label class="view-control">Question: <strong class="short-text">${record.question}</strong></label>
                    <label class="view-control">Answers: <strong class="long-text">[${record.answers}]</strong></label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');
    }
</script>