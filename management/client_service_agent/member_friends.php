<?php include "./layout/layout.php"; ?>

<script src="../../resources/js/matching_algorithm.js"></script>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Friends";
    document.getElementById("add-item-button").style.display = "none";

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="#" onClick="window.history.back();">Members</a></li>
                            <li><a href="member_friendsr.php?id=${memberId}">Friends</a></li>`;

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
            "url": getURL("matched_friends/get_member_friends.php?id=" + memberId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "friend", "render": function (data) { return data.name } },
            { "data": "friend", "render": function (data) { return data.NIC } },
            { "data": "friend", "render": function (data) { return data.email }},
            { "data": "friend", "render": function (data) { return `<div class="td-center">${data.mobileNo}</div>` } },
            { "data": "status", "render": function (data) { return getMatchedFriendStatusTextStyled(data)} },
            {
                "data": {"id": 'id', "status":'status', "friend": 'friend', "memberId": 'memberId'},
                "render": function (data) {
                    var statusHtml = ``;

                    if(data.status == 1){
                        statusHtml = `
                                        <a href="#" onclick="triggerRateModal('${data.id}', '${data.friend.id}')"><i class="fas fa-star-half-alt"></i>Rate Match</a>
                                        <a href="#" onclick="triggerUnfriendModal('${data.id}')"><i class="fas fa-window-close red"></i>Unfriend</a>
                                    `;
                    }else{
                        statusHtml = `
                                        <a href="#" onclick="cancelRequest('${data.id}')"><i class="fas fa-window-close red"></i>Cancel Request</a>`
                    }

                    if(memberId != data.memberId && data.status != 1){
                        statusHtml += `<a href="#" onclick="confirmRequest('${data.id}')"><i class="fas fa-check-circle green"></i>Confirm</a>`;
                    }

                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.friend.id}', ${data.status})"><i class="fas fa-eye lightblue"></i>View Details</a>
                                    ${statusHtml}
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No friends found"
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

    const confirmRequest = async (id) => {
        try{
            var result = await fetch(getURL("matched_friends/confirm_request.php?id=" + id));
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
        }
    }
    
    const cancelRequest = async (id) => {
        try{
            var result = await fetch(getURL("matched_friends/cancel_request.php?id=" + id));
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
        }
    }

</script>

<!-- functions for viewing record details -->
<script>
    const triggerViewModal = async (friendId, status) => {
        var friend = await calculateMatch(memberId, friendId);
        viewRecordDetails(friend, status);
    }

    const viewRecordDetails = (friend, status) => {
        var categoryPercentage = "";

        friend.surveyCategories.forEach(sc => {
            categoryPercentage += `#${sc.name}: ${sc.percentage}% </br>`;
        });

        document.querySelector('.modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>`;

        document.querySelector('#modalLongTitle').innerHTML = "Friend Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../../resources/images/uploads/profile/${friend.image}" alt="">
                </div>
                <div class="col-6">
                    <label class="view-control">Matching Percentage: <strong class="short-text">${friend.overallPercentage}%</strong></label>
                    <label class="view-control">Name: <strong class="short-text">${friend.name}</strong></label>
                    <label class="view-control">Gender: <strong class="short-text">${friend.gender}</strong></label>
                    <label class="view-control">Friend Status: <strong class="short-text">${getMatchedFriendStatusTextStyled(status)}</strong></label>
                    <label class="view-control">Specific Matchings: 
                        <strong class="long-text">
                            ${categoryPercentage}
                        </strong>
                    </label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;

        $('#ModalCenter').modal('toggle');
    }
</script>

<!-- functions for unfriending -->
<script>
    const triggerUnfriendModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once unfriended, you will have to send friend request again to be friend!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    unfriend(id);
                }
            });
    }

    const unfriend = async (id) => {
        showLoaderOnViewPort("Removing user...");

        try{
            var result = await fetch(getURL("matched_friends/unfriend.php?id=" + id));
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

<!-- functions for rating friend match -->
<script>
    const triggerRateModal = async (matchId, friendId) => {

        var match = await fetch(getURL("matched_friends/read_one.php?id=" + matchId));
        match = await match.json();
        match = match.record;

        document.querySelector('#modalLongTitle').innerHTML = "Feedback";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="addRecord('${matchId}');" class="btn btn-primary">Save changes</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="matchFeedback">Is the match a good match? <span style="color:red;">*</span></label>
                        <select id="matchFeedback" class="form-control" required="required">
                            <option selected disabled value="">Select Feedback</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        document.querySelector('#model-wrapper').classList.remove("modal-lg");

        if(match.memberId == memberId && match.isGoodMatchRequester != null){
            document.getElementById("matchFeedback").value = match.isGoodMatchRequester;
        }else if(match.friendId == memberId && match.isGoodMatchRecevier != null){
            document.getElementById("matchFeedback").value = match.isGoodMatchRecevier;
        }

        $('#ModalCenter').modal('toggle');
    }

    const addRecord = async (matchId) => {
        showLoader("addRecordBtn", "Submitting...")

        var formData = {
            "id": matchId,
            "memberId": memberId,
            "feedback": document.getElementById("matchFeedback").value
        }

        try{
            var result = await fetch(getURL("matched_friends/match_feedback.php"), { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            result = await result.json();

            if(result.succ){
                $('#ModalCenter').modal('toggle');
                toastr.success(result.msg, "Sucess!");
                rePopulateTable();
                document.querySelector('#model-wrapper').classList.add("modal-lg");
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
