<?php 

require_once "./api/automated_functions/process_payments.php";
require_once "./api/automated_functions/process_event_attendance.php";

session_start();

if(isset($_SESSION['userId'])){
    unset($_SESSION['userId']);
    unset($_SESSION['role']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sidebar template</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />

    <link rel="stylesheet" href="./resources/css/management_login.css">
</head>

<body>
<div class="container">
        <br>
        <div class="card card-container">
            <form class="form-signin" method="post">
                <span id="reauth-email" class="reauth-email"></span>
                <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required autofocus>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required>
                <input type="text" id="nic" name="nic" class="form-control" placeholder="Your NIC" required>
                <input type="number" id="mobileNo" name="mobileNo" class="form-control" placeholder="Your Mobile No" required>
                <select name="gender" id="gender" class="form-control">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="text" id="address" name="address" class="form-control" placeholder="Your Address" required>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" required>
                <input class="btn btn-lg btn-primary btn-block btn-signin" name="submit" type="submit" value="Signup">
                <a href="index.php" class="forgot-password">
                    Have an account? Login.
                </a>
            </form><!-- /form -->
            
        </div><!-- /card-container -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</body>

</html>


<?php 

require_once "config/database.php";
require_once "models/staff.php";
require_once "models/survey.php";
require_once "models/member_survey.php";

if(isset($_POST['submit'])){
    if(
        isset($_POST['name']) && 
        isset($_POST['email']) && 
        isset($_POST['nic']) && 
        isset($_POST['mobileNo']) && 
        isset($_POST['gender']) && 
        isset($_POST['address']) && 
        isset($_POST['password']) 
    ){
        $database = Database::getInstance();
        $member = new Member($database->getConnection());
        $survey = new Survey($database->getConnection());
        $memberSurvey = new MemberSurvey($database->getConnection());

        $member->id = generateMemberId();
        $member->name = $_POST['name'];
        $member->NIC = $_POST['nic'];
        $member->gender = $_POST['gender'];
        $member->email = $_POST['email'];
        $member->mobileNo = $_POST['mobileNo'];
        $member->address = $_POST['address'];
        $member->memberType = 1;
        $member->password = md5($_POST['password']);
        $member->image = "user.jpg";

        if($member->createOnlineMember()){
            $_SESSION['signup_msg'] = "Account Created Successfully!";

            $memberSurvey->memberId = $member->id;
            $survey_arr = $survey->readAll()->fetchAll();
            $memberSurvey->createMultiple($survey_arr);

            header("Location: index.php");
        }else{
            echo "<script>toastr.error('Unable to create account. Please try again!', 'Error');</script>";
        }
    }else{
        echo "<script>toastr.error('Invalid Details. Please input all details.', 'Error');</script>";
    }
}

function generateMemberId(){
    $myfile = fopen("./api/members/member_id.txt", "r") or die("Unable to open file!");
    $member_id =  fgets($myfile);
    fclose($myfile);
    $member_num = preg_replace('/[^0-9]/', '', $member_id) + 1;
    $member_id = "MEM".$member_num;

    $myfile = fopen("./api/members/member_id.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $member_id);
    fclose($myfile);

    return $member_id;
}

?>


