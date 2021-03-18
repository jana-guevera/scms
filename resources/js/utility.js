// function for showing loader on a button
const showLoader = (buttonId, text) => {
    var buttonInput = document.getElementById(buttonId);
    buttonInput.disabled = true;

    buttonInput.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    ${text}
                `;
}

// function for stopping the loader on a button
const hideLoader = (buttonId, name) => {
    var button = document.getElementById(buttonId);
    button.innerHTML = name;
    button.disabled = false;
}

// function for getting the user role 
const getUserRole = (role) => {
    if(role == 0){
        return "Admin";
    }

    if(role == 1){
        return "Receptionist";
    }

    if(role == 2){
        return "Client Service Agent";
    }

    if(role == 3){
        return "Senior Client Service Agent";
    }

    if(role == 4){
        return "Finance Manager";
    }

    return "Unknown";
}

// get member status text with colors and styles
const getStatusStyled = (status) => {
    
    if(status == 0){
        return `<div class="td-center" style="color:red;">Blocked</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Active</div>`;
    }

    if(status == 3){
        return `<div class="td-center" style="color:red;">Unpaid</div>`;
    }
}

// get monthly payment status text with colors and styles
const getMonthlyPaymentStatusStyled = (status) => {
    
    if(status == 0){
        return `<div class="td-center" style="color:red;">Unpaid</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Paid</div>`;
    }

    if(status == 2){
        return `<div class="td-center" style="color:red;">Overdue</div>`;
    }
}

// get member status text
const getStatus = (status) => {

    if(status == 0){
        return "Blocked";
    }

    if(status == 1){
        return "Active";
    }
}

// get event status styled 
const getEventStatusStyled = (status) => {
    
    if(status == 0){
        return `<div class="td-center" style="color:red;">Cancelled</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Active</div>`;
    }
    
    if(status == 3){
        return `<div class="td-center" style="color:green;">Completed</div>`;
    }
}

// get member event status styled 
const getMemberEventStatusStyled = (status) => {
    
    if(status == 0){
        return `<div class="td-center" style="color:blue;">Attending</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Attended</div>`;
    }

    if(status == 2){
        return `<div class="td-center" style="color:red;">Didn't Attent</div>`;
    }
}

// get online member or offline member text
const getOnlineOfflineMemberText = (type) => {
    if(status == 0){
        return "Offline Member";
    }

    if(status == 1){
        return "Online Member";
    }
}

// function for getting input type name
const getInputTypeText = (type) => {
    if(type == 0){
        return "Binary Question";
    }

    if(type == 1){
        return "Multi-Answer Question";
    }

    if(type == 2){
        return "Single Answer Question";
    }
}

// function for getting matched_friends status
const getMatchedFriendStatusTextStyled = (status) => {
    if(status == 0){
        return `<div class="td-center" style="color:blue;">Pending Confirmation</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Friends</div>`;
    }
}

const getMemberScheduleStatusTextStyled = (status) => {
    if(status == 0){
        return `<div class="td-center" style="color:blue;">Pending Confirmation</div>`;
    }

    if(status == 1){
        return `<div class="td-center" style="color:green;">Confirmed</div>`;
    }

    if(status == 2){
        return `<div class="td-center" style="color:red;">Cancelled</div>`;
    }

    if(status == 3){
        return `<div class="td-center" style="color:red;">Declined</div>`;
    }
}

const getMemberScheduleStatusText = (status) => {
    if(status == 0){
        return `Pending Confirmation`;
    }

    if(status == 1){
        return `Confirmed`;
    }

    if(status == 2){
        return `Cancelled`;
    }

    if(status == 3){
        return `Declined`;
    }
}

// function to remove the row menu 
const removeRecordMenu = (event) => {
    var btn = document.getElementById(`record-button-${currentRowMenu}`);

    if (event.target != btn) {
    
        document.querySelector("#dropdown-wrapper-" + currentRowMenu).style.display = "none";
        window.removeEventListener("click", removeRecordMenu);
    }
}

// function to change to the time to string format eg: 9:00 AM to 12:30 PM
const timeChange = (time) => {
    var split_time = time.split(":");
    var hours = split_time[0];
    var minutes = split_time[1];
    var meridian;

    if (hours >= 12) {
        meridian = "PM";
        if (hours != 12) {
            hours -= 12;
        }
    } else if (hours < 12) {
        meridian = "AM";
        if (hours == 0) {
            hours = 12;
        }
        if (hours < 10) {
            hours = hours.replace("0", "");
        }
    }

    return `${hours}:${minutes} ${meridian}`;
}

// function to convert time to readable format
const dateTimeChange = (datetime) => {
    var dateTimeArray = datetime.split("T");
    var date = dateTimeArray[0];
    var time = dateTimeArray[1];

    return `${date} -- ${timeChange(time)}`;
}

// function for showing and hiding loader on the view port
const showLoaderOnViewPort = (msg) => {

}

const hideLoaderOnViewPort = (msg) => {

}

