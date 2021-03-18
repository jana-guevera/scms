<?php include "./layout/layout.php"; ?>

<script src="../../resources/js/jspdf.min.js"></script>
<script src="../../resources/js/jspdf.plugin.autotable.js"></script>

<!-- page layout and table populating -->
<script>
    document.getElementById("nav-dashboard").classList.add("active-page");
    document.getElementById("add-item-button").style.display = "none";
    document.getElementById("pageTitle").textContent = "Dashboard";

    var html = `
        <div class="row">
            <div class="col-md-3">
                <div class="card-counter primary">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers count-offline-members">12</span>
                    <span class="count-name"><a href="offline_members.php">Offline Members</a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers count-online-members">12</span>
                    <span class="count-name"><a href="online_members.php">Online Members</a></span>
                </div>
            </div>
        </div>
    `;

    document.getElementById("table-wrapper").innerHTML = html;

    const fetchData = async () => {
        var offlineMembers = await fetch(getURL("members/read_all_manual_members.php"));
        offlineMembers = await offlineMembers.json();
        document.querySelector(".count-offline-members").innerHTML = offlineMembers.records.length;

        var onlineMembers = await fetch(getURL("members/read_all_online_members.php"));
        onlineMembers = await onlineMembers.json();
        document.querySelector(".count-online-members").innerHTML = onlineMembers.records.length;
    }

    fetchData();
</script>