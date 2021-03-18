<?php 

require_once "./api/automated_functions/process_payments.php";
require_once "./api/automated_functions/process_event_attendance.php";

session_start();

if(isset($_SESSION['userId'])){
    unset($_SESSION['userId']);
    unset($_SESSION['name']);
    unset($_SESSION['image']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sussex Companions</title>
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
                <input type="password" id="password" name="password" class="form-control mb-0" placeholder="Password" required>
                <a href="#" class="forgot-password">
                    Forgot the password?
                </a>
                <div id="remember" class="checkbox mt-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <input class="btn btn-lg btn-primary btn-block btn-signin" name="submit" type="submit" value="Sign in">
                <a href="signup.php" class="forgot-password">
                    Dont have an account?
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

if(isset($_SESSION['signup_msg']) && $_SESSION['signup_msg'] != ""){
    echo "<script>toastr.success('Account Created Successfully!', 'Success');</script>";
    $_SESSION['signup_msg'] = "";
}

require_once "config/database.php";
require_once "models/member.php";

if(isset($_POST['submit'])){
    if(isset($_POST['email']) && isset($_POST['password'])){
        $database = Database::getInstance();
        $user = new Member($database->getConnection());

        $user->email = $_POST['email'];
        $user->password = md5($_POST['password']);

        $stmt = $user->login();
        $numRows = $stmt->rowCount();

        if($numRows == 1){
            $fetchedUser = $stmt->fetchAll()[0];
            if($fetchedUser['status'] == 1 && $fetchedUser['memberType'] == 1){
                $_SESSION['userId'] = $fetchedUser['id'];
                $_SESSION['name'] = $fetchedUser['name'];
                $_SESSION['image'] = $fetchedUser['image'];

                header("Location: ./member/events.php");
            }else{
                echo "<script>toastr.error('Your account has been blocked. Please contact the administrator!', 'Error');</script>";
            }
        }else{
            echo "<script>toastr.error('Invalid Credentials. Please input valid credentials.', 'Error');</script>";
        }
    }else{
        echo "<script>toastr.error('Invalid Credentials. Please input all credentials.', 'Error');</script>";
    }
}

?>


