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
                    <i class="fas fa-ticket-alt"></i>
                    <span class="count-numbers count-events">12</span>
                    <span class="count-name"><a href="events.php">Events</a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter info">
                    <i class="fa fa-list-alt"></i>
                    <span class="count-numbers count-categories">12</span>
                    <span class="count-name"><a href="event_categories.php">Event Categories</a></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter success">
                    <i class="fas fa-check-circle"></i>
                    <span class="count-numbers count-successful-matches">12</span>
                    <span class="count-name"><a href="#" onclick="triggerInputModal();">Successful Matches</a></span>
                </div>
            </div>
        </div>
    `;

    document.getElementById("table-wrapper").innerHTML = html;

    const fetchData = async () => {
        var events = await fetch(getURL("events/read_all.php"));
        events = await events.json();
        document.querySelector(".count-events").innerHTML = events.records.length;

        var categories = await fetch(getURL("event_categories/read_all.php"));
        categories = await categories.json();
        document.querySelector(".count-categories").innerHTML = categories.records.length;

        var unsuccessfulMatches = await fetch(getURL("matched_friends/get_successful_matches.php"));
        unsuccessfulMatches = await unsuccessfulMatches.json();
        document.querySelector(".count-successful-matches").innerHTML = unsuccessfulMatches.records.length;
    }

    fetchData();
</script>

<!-- function for generating unsuccessful matches report -->
<script>
    const triggerInputModal = () => {
        document.querySelector('#model-wrapper').classList.remove("modal-lg");

        document.querySelector('#modalLongTitle').innerHTML = "Successful Matches Report";

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
        var formData = {
            "startMonth": document.getElementById("startMonth").value + "-01",
            "endMonth": document.getElementById("endMonth").value + "-01" 
        }

        var result = await fetch(getURL("matched_friends/get_successful_matches_between_momths.php"), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        });

        result = await result.json();
        $('#ModalCenter').modal('toggle');
        downloadReport(result.records);
    }   

    const downloadReport = (records) => {
        var doc = new jsPDF('l', 'pt', 'a4');

        var pdf = [];

        records.forEach(record => {
            var temp = [
                record.feedBackProvider.name, 
                record.feedBackProvider.email, 
                record.friend.name,
                record.friend.email,
                record.created_at
            ]; 

            pdf.push(temp);
        });

        doc.text(40, 30, "Sucessful Matches");

        doc.autoTable({
            margin: { top: 50 },
            head: [
                ['Feedback By', 'Email', 'Friend', 'Friend Email', "Date"],
            ],
            body:pdf ,
        });

        doc.save(`Successful_matches.pdf`);
    }
</script>