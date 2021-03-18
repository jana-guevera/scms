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
                <div class="card-counter info">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers count-online-members">12</span>
                    <span class="count-name"><a href="online_members.php">Online Members</a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter danger">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="count-numbers count-events">12</span>
                    <span class="count-name"><a href="events.php">Events</a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="fas fa-money-check-alt"></i>
                    <span class="count-numbers count-events-income">12</span>
                    <span class="count-name"><a href="#" onclick="triggerInputModal();">Events Income</a></span>
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

        var events = await fetch(getURL("events/read_all.php"));
        events = await events.json();
        document.querySelector(".count-events").innerHTML = events.records.length;

        var totalIncome = await fetch(getURL("events/get_events_income.php"));
        totalIncome = await totalIncome.text();
        document.querySelector(".count-events-income").innerHTML = "$" + totalIncome;
    }

    fetchData();
</script>

<!-- function for generating unsuccessful matches report -->
<script>
    const triggerInputModal = () => {
        document.querySelector('#model-wrapper').classList.remove("modal-lg");

        document.querySelector('#modalLongTitle').innerHTML = "Events Income Report";

        document.querySelector('.modal-footer').innerHTML = `
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" id="addRecordBtn" onclick="getReport();" class="btn btn-primary">Get Report</button>`;

        var htmlContent = 
        ` <form onsubmit="addRecord(); return false;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="startMonth">Start Month<span style="color:red;">*</span></label>
                        <input type="month" id="startMonth" class="form-control" required="required">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="endMonth">End Month<span style="color:red;">*</span></label>
                        <input type="month" id="endMonth" class="form-control" required="required">
                    </div>
                </div>
            </div>
        </form>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;

        $('#ModalCenter').modal('toggle');
    }

    const getReport = async () => {
        $('#ModalCenter').modal('toggle');

        var formData = {
            "startMonth": document.getElementById("startMonth").value + "-01",
            "endMonth": document.getElementById("endMonth").value + "-01" 
        }

        var result = await fetch(getURL("events/get_events_income_between_momths.php"), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        });

        result = await result.json();

        if(result.records.length > 0){
            downloadReport(result, formData);
        }else{
            toastr.error("No events have been shcedules during the selected period!");
        }
       
    }   

    const downloadReport = (data, formData) => {
        var doc = new jsPDF('l', 'pt', 'a4');

        var pdf = [];

        data.records.forEach(record => {
            var temp = [
                record.name, 
                record.categoryName, 
                dateTimeChange(record.startDateTime),
                dateTimeChange(record.endDateTime),
                "$" + record.eventIncome
            ]; 

            pdf.push(temp);
        });

        doc.text(40, 30, "Events Income - " + data.startMonth + " to " + data.endMonth);

        doc.autoTable({
            margin: { top: 50 },
            head: [
                ['Event Name', 'Category', 'Start Datetime', 'End Datetime', "Income"],
            ],
            body:pdf ,
        });

        doc.save(`events_income.pdf`);
    }
</script>