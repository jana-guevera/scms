<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: ../../index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sussex Companions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

    <link rel="stylesheet" href="../resources/css/management.css">
</head>

<body>
    <div class="page-wrapper chiller-theme toggled">
        <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
            <i class="fas fa-bars"></i>
        </a>

        <!-- sidebar-wrapper  -->
        <nav id="sidebar" class="sidebar-wrapper">
            <!-- sidebar-header  -->
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <a href="#">Friends Matcher</a>
                    <div id="close-sidebar">
                        <i class="fas fa-times"></i>
                    </div>
                </div>

                <div class="sidebar-header">
                    <div class="user-pic">
                        <img class="img-responsive rounded-circle profile-image" src="../resources/images/uploads/profile/<?php echo $_SESSION['image']; ?>" alt="User picture">
                    </div>
                    <div class="user-info">
                        <span class="user-name">
                            <strong><?php echo $_SESSION['name']; ?></strong>
                        </span>
                        <span class="user-role">Member</span>
                        <span class="user-status">
                            <i class="fa fa-circle"></i>
                            <span>Online</span>
                        </span>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>Navigation</span>
                        </li>

                        <li class="sidebar-dropdown" id="nav-events">
                            <a href="#">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Events</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <li>
                                        <a href="my_events.php">My Events

                                        </a>
                                    </li>
                                    <li>
                                        <a href="events.php">Upcoming Events</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li id="nav-survey">
                            <a href="survey.php">
                                <i class="fas fa-poll"></i>
                                <span>Survey</span>
                            </a>
                        </li>

                        <li class="sidebar-dropdown" id="nav-friends">
                            <a href="#">
                                <i class="fa fa-users"></i>
                                <span>Friends</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <li>
                                        <a href="my_friends.php">My Friends

                                        </a>
                                    </li>
                                    <li>
                                        <a href="find_friends.php">Find Friends</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li id="nav-schedules">
                            <a href="schedules.php">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Schedules</span>
                            </a>
                        </li>

                        <li id="nav-payments">
                            <a href="payments.php">
                                <i class="fas fa-money-bill"></i>
                                <span>Payments</span>
                            </a>
                        </li>

                        <li id="nav-notifications">
                            <a href="notifications.php">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>Notifications</span>
                                <span class="badge badge-pill badge-warning notification" id="unread-notification">3</span>
                            </a>
                        </li>

                        <li id="nav-profile">
                            <a href="profile_setting.php">
                                <i class="fas fa-user-cog"></i>
                                <span>Profile Settings</span>
                            </a>
                        </li>

                        <li>
                            <a href="../index.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- sidebar-footer  -->
            <!-- <div class="sidebar-footer">
                <a href="#">
                    <i class="fa fa-bell"></i>
                    <span class="badge badge-pill badge-warning notification">3</span>
                </a>
                <a href="#">
                    <i class="fa fa-envelope"></i>
                    <span class="badge badge-pill badge-success notification">7</span>
                </a>
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span class="badge-sonar"></span>
                </a>
                <a href="#">
                    <i class="fa fa-power-off"></i>
                </a>
            </div> -->

        </nav>

        <!-- page-content" -->
        <main class="page-content">
            <div class="container-fluid" style="min-height: 1000px;">

                <div class="page-header">
                    <h2 id="pageTitle">Pro Sidebar</h2>
                    <div>
                        <ul id="breadcumbs">
                            <li><a href="#">Home</a></li>
                        </ul>
                    </div>
                </div>

                <hr>

                <div id="main-bottom">
                    <div id="add-button-wrapper">
                        <button class="btn btn-success mb-4" id="add-item-button" type="button" onclick="triggerAddNew()">
                            Add Course
                        </button>
                    </div>

                    <div class="card p-3" id="table-wrapper">
                        <table id="table_id" class="stripe cell-border hover">
                            <thead id="table-head-id">

                            </thead>
                            <tbody id="table-body-id">
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <div class="modal h-100 fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="model-wrapper">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLongTitle">Add User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <!-- <form onsubmit="addRecord(); return false;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Name <span style="color:red;">*</span></label>
                                                    <input type="text" id="name" class="form-control" placeholder="Staff Name" required="required">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nic">NIC <span style="color:red;">*</span></label>
                                                    <input type="text" id="nic" class="form-control" placeholder="Staff NIC" required="required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email <span style="color:red;">*</span></label>
                                                    <input type="email" id="email" class="form-control" placeholder="Staff Email" required="required">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mobileNo">Mobile No <span style="color:red;">*</span></label>
                                                    <input type="number" id="mobileNo" class="form-control" placeholder="Staff Mobile No" required="required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="role">Role <span style="color:red;">*</span></label>
                                                    <select name="role" id="role" class="form-control" required="required">
                                                        <option selected disabled value="">Select user role</option>
                                                        <option value="0">Admin</option>
                                                        <option value="1">Receptionist</option>
                                                        <option value="2">Client Service Agent</option>
                                                        <option value="3">Senior Client Service Agent</option>
                                                        <option value="4">Finance Manager</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="address">Address <span style="color:red;">*</span></label>
                                                    <input type="text" id="address" class="form-control" placeholder="Staff Address" required="required">
                                                </div>
                                            </div>
                                        </div>
                                    </form> -->
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onclick="addRecord();" class="btn btn-primary">Save changes</button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="../resources/js/management.js"></script>
    <script src="../config/api.js"></script>
    <script src="../resources/js/utility.js"></script>

    <script>
        const fetchUnreadNotification = async () => {
            const memberId = "<?php echo $_SESSION['userId'];?>";
            var result = await fetch(getURL("notifications/get_unread_notifications.php?id=" + memberId));
            result = await result.json();
            result = result.records;

            var alert = document.getElementById("unread-notification");

            if(result.length > 0){  
                alert.style.display = "block";
                alert.innerHTML = result.length;
            }else{
                alert.style.display = "none";
            }

            setTimeout(fetchUnreadNotification, 5000);
        }

        fetchUnreadNotification();
    </script>

</body>

</html>