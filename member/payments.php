<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_SESSION['userId'];?>";
    var currentRowMenu = 0;

    document.getElementById("nav-payments").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";
    document.getElementById("pageTitle").textContent = "Payments";

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
                "data": {"monthId" : 'monthId', "overallStatus" : 'overallStatus', "balanceAmount" : 'balanceAmount'},
                "render": function (data) {
                    var html = `<a href="payment_invoice.php?id=${memberId}&monthId=${data.monthId}" target="_blank"><i class="fas fa-file-invoice lightblue"></i>View Bill</a>`;

                    if(data.overallStatus != 1){
                        html = `<a href="make_payment.php?userId=${memberId}&monthId=${data.monthId}&amount=${data.balanceAmount}"><i class="fas fa-money-bill-alt green"></i>Make Payment</a>` + html;
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
</script>