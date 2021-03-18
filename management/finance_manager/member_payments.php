<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";
    const monthId = "<?php echo $_GET['monthId'];?>";

    var currentRowMenu = 0;

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="offline_members.php">Members</a></li>
                            <li><a href="member_payments_month.php?id=${memberId}">Payments Months</a></li>
                            <li><a href="member_payments.php?id=${memberId}&monthId=${monthId}">Payments</a></li>`;

    const fetchData = async () => {
        var result = await fetch(getURL("members/read_one.php?id=" + memberId));
        result = await result.json();

        document.getElementById("pageTitle").textContent = "Payments -- " + result.record.name;
    }

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th class="td-center">ID</th>
            <th>Month</th>
            <th>Payment Type</th>
            <th>Total Amount $</th>
            <th class="td-center">Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("payments/monthly_payments.php?id=" + memberId + "&monthId=" + monthId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "month" },
            { "data": "paymentFor" },
            { "data": "amount" },
            { "data": "status", "render": function (data) {return getMonthlyPaymentStatusStyled(data)}},
            {
                "data": {"id" : 'id', "paymentFor" : 'paymentFor'},
                "render": function (data) {
                    return `
                        <div class="td-right">
                            <button id="record-button-${data.id}" onclick="showRecordMenu('${data.id}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.id}">
                                <div class="record-dropdown">
                                    <a href="#" onclick="triggerViewModal('${data.id}', '${data.paymentFor}')"><i class="fas fa-eye lightblue"></i>View Details</a>
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

    fetchData();
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