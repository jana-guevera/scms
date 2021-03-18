<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";
    var currentRowMenu = 0;

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="offline_members.php">Members</a></li>
                            <li><a href="member_payments_month.php?id=${memberId}">Payments</a></li>`;

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
            <th>Total Amount $</th>
            <th>Balance Payment $</th>
            <th class="td-center">Payment Status</th>
            <th style="text-align:right"></th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("payments/payments_months.php?id=" + memberId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "id", "render": function (data) { return `<div class="td-center">${data}</div>` } },
            { "data": "month" },
            { "data": "totalAmount" },
            { "data": "balanceAmount" },
            { "data": "overallStatus", "render": function (data) {return getMonthlyPaymentStatusStyled(data)}},
            {
                "data": {"monthId" : 'monthId', "overallStatus" : 'overallStatus'},
                "render": function (data) {
                    var html = `<a href="payment_invoice.php?id=${memberId}&monthId=${data.monthId}" target="_blank"><i class="fas fa-file-invoice lightblue"></i>View Bill</a>`;

                    if(data.overallStatus == 2){
                        html += `<a href="payment_overdue_letter.php?id=${memberId}&monthId=${data.monthId}" target="_blank"><i class="fas fa-file-invoice red"></i>Create Overdue Bill</a>`;
                    }

                    if(data.overallStatus != 1){
                        html = `<a href="#" onclick="triggerPaymentModal('${data.monthId}')"><i class="fas fa-money-bill-alt green"></i>Make Payment</a>` + html;
                    }

                    return `
                        <div class="td-right">
                            <button id="record-button-${data.monthId}" onclick="showRecordMenu('${data.monthId}')" class="record-dropdown-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="record-dropdown-wrapper" id="dropdown-wrapper-${data.monthId}">
                                <div class="record-dropdown">
                                    ${html}
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "language": {
            "emptyTable": "No payments months found"
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

<!-- functions for making payment -->
<script>
    const triggerPaymentModal = (id) => {
        swal({
            title: "Are you sure?",
            text: "Once the payment has been done, it cannot be reversed!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    makePayment(id);
                }
            });
    }

    const makePayment = async (id) => {
        showLoaderOnViewPort("Making Payment...");

        try{
            var result = await fetch(getURL("payments/make_payments.php?id=" + memberId + "&monthId=" + id));
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