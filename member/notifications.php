<?php include "./layout/layout.php"; ?>

<style>
thead th:last-child{
  width: 150px;
}
</style>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_SESSION['userId'];?>";

    document.getElementById("nav-notifications").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Notifications";
    document.getElementById("add-item-button").style.display = "none";

    // set table head
    $("#table-head-id").append(
        `<tr>
            <th>Notification</th>
            <th>Date</th>
        </tr>`
    );

    // function to fetch data from the database and populate table
    dataTable = $("#table_id").DataTable({
        "ajax": {
            "url": getURL("notifications/get_member_notification.php?id=" + memberId),
            "type": "GET",
            "datatype": "json",
            "dataSrc": "records"
        },
        "columns": [
            { "data": "notification", "render": function (data) { return data; } },
            { "data": "created_at", "render": function (data) { return data; } },
        ],
        "language": {
            "emptyTable": "No notifications were found"
        },
        "width": "100%"
    });

    // change notifications status to read
    fetch(getURL("notifications/change_status.php?id=" + memberId));

</script>