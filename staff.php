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
        <br><br><br>
        <div class="card card-container">
            <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" method="post">
                <span id="reauth-email" class="reauth-email"></span>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required autofocus>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <div id="remember" class="checkbox">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <input class="btn btn-lg btn-primary btn-block btn-signin" name="submit" type="submit" value="Sign in">
            </form><!-- /form -->
            <a href="#" class="forgot-password">
                Forgot the password?
            </a>
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

if(isset($_POST['submit'])){
    if(isset($_POST['email']) && isset($_POST['password'])){
        $database = Database::getInstance();
        $user = new Staff($database->getConnection());

        $user->email = $_POST['email'];
        $user->password = md5($_POST['password']);

        $stmt = $user->login();
        $numRows = $stmt->rowCount();

        if($numRows == 1){
            $fetchedUser = $stmt->fetchAll()[0];
            $_SESSION['userId'] = $fetchedUser['id'];
            $_SESSION['role'] = $fetchedUser['role'];

            if($fetchedUser['role'] == 0){
                header("Location: ./management/admin/dashboard.php");
            }else if($fetchedUser['role'] == 1){
                header("Location: ./management/receptionist/dashboard.php");
            }else if($fetchedUser['role'] == 2){
                header("Location: ./management/client_service_agent/dashboard.php");
            }else if($fetchedUser['role'] == 3){
                header("Location: ./management/senior_client_service_agent/dashboard.php");
            }else if($fetchedUser['role'] == 4){
                header("Location: ./management/finance_manager/dashboard.php");
            }
        }else{
            echo "<script>toastr.error('Invalid Credentials. Please input valid credentials.', 'Error');</script>";
        }
    }else{
        echo "<script>toastr.error('Invalid Credentials. Please input all credentials.', 'Error');</script>";
    }
}

?>


