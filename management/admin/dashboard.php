<?php include "./layout/layout.php"; ?>

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
                    <span class="count-numbers count-staffs">12</span>
                    <span class="count-name">Staffs</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter danger">
                    <i class="fas fa-poll"></i>
                    <span class="count-numbers count-survey">12</span>
                    <span class="count-name">Survey</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="fa fa-list-alt"></i>
                    <span class="count-numbers count-categories">12</span>
                    <span class="count-name">Staffs</span>
                </div>
            </div>
        </div>
    `;

    document.getElementById("table-wrapper").innerHTML = html;

    const fetchData = async () => {
        var staffs = await fetch(getURL("staffs/read_all.php"));
        staffs = await staffs.json();
        document.querySelector(".count-staffs").innerHTML = staffs.records.length;

        var survey = await fetch(getURL("survey/read_all.php"));
        survey = await survey.json();
        document.querySelector(".count-survey").innerHTML = survey.records.length;

        var categories = await fetch(getURL("survey_categories/read_all.php"));
        categories = await categories.json();
        document.querySelector(".count-categories").innerHTML = categories.records.length;
    }

    fetchData();
</script>