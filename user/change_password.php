<?php
ob_start(); 
session_start();
include ('includes/header.php');
include ('includes/navbar.php');
require_once ('../connections/connection.php');

$con = connection();
// Redirect to login page if the session is not set START
if (!isset($_SESSION['STAFFID'])) {
    header("Location: ../index.php");
    exit();
}
// Redirect to login page if the session is not set END

// TO DISPLAY THE INFO USING SESSION ID START
$user_id = $_SESSION['STAFFID'];
$sql = "SELECT * FROM STAFF WHERE STAFFID = '$user_id'";
$query = $con->query($sql);
$row = $query->fetch_assoc();
// TO DISPLAY THE INFO USING SESSION ID END


if (isset($_POST['change-password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch stored password
    $sqlquery = "SELECT PASSWORD FROM STAFF WHERE STAFFID = '$user_id'";
    $result = mysqli_query($con, $sqlquery);
    $rows = $result->fetch_assoc();

    if ($rows['PASSWORD'] === md5($current_password)) {  
        if ($new_password === $confirm_password) {
            $hashed_password = md5($new_password);
            $sqlupdate = "UPDATE STAFF SET PASSWORD = '$hashed_password' WHERE STAFFID = '$user_id'";
            if (mysqli_query($con, $sqlupdate)) {
                $_SESSION['toastr'] = "toastr.success('Password changed successfully!');";
            } else {
                $_SESSION['toastr'] = "toastr.error('Error updating password. Please try again.');";
            }
        } else {
            $_SESSION['toastr'] = "toastr.warning('New password and confirm password do not match.');";
        }
    } else {
        $_SESSION['toastr'] = "toastr.error('Current password is incorrect.');";
    }

    // **Redirect to prevent form resubmission**
    header("Location: change_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <!-- Toastr CSS START -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
 <!-- Toastr CSS END-->
</head>
<body>
<div id="content-wrapper" class="d-flex flex-column">
           
           <!-- Topbar -->
              <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
          
          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
              <i class="fa fa-bars"></i>
          </button>
          
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
              <!-- Nav Item - User Information -->
              <li class="nav-item dropdown no-arrow">
                  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$row['FIRST_NAME']?></label></span>
                      <img class="img-profile rounded-circle"
                          src="../admin/images/<?=$row['PROFILE']?>">
                  </a>
                  <!-- Dropdown - User Information -->
                  <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                      aria-labelledby="userDropdown">
                      <a class="dropdown-item" href="profile.php">
                          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                          Profile
                      </a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                          Logout
                      </a>
                  </div>
              </li>
          </ul>
          </nav>
       

  <!-- Change Password Form -->
<div class="container d-flex justify-content-center align-items-center" style="margin-top:5rem">
    <div class="row shadow-lg p-5 bg-white rounded w-100" style="max-width: 800px;">
        <!-- Left Side (Image) -->
        <div class="col-md-5 d-none d-md-block text-center" style="display: flex;align-items:Center">
        <i class="fa-solid fa-lock fa-fade fa-xl" style="color: #c5c4bf;font-size:12rem;margin-top:10rem"></i>
     
        </div>

        <!-- Right Side (Form) -->
        <div class="col-md-6">
            <h2 class="text-center mb-4">Change Password</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="current-password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current-password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new-password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new-password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                </div>
                <button type="submit" name="change-password" class="btn w-100" style="background-color: #283593;color:White">Change Password</button>
            </form>
        </div>
    </div>
</div>




          </div>
        </div>
          <!-- End of Topbar -->


<!-- Scripts FOR TOAST START -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    <?php 
        if (!empty($_SESSION['toastr'])) {
            echo $_SESSION['toastr']; 
            unset($_SESSION['toastr']); // Clear session message after displaying
        }
    ?>
</script>
<!-- Scripts FOR TOAST END -->



</body>
</html>







<?php 
include ('includes/script.php');
// include ('includes/footer.php');
ob_end_flush();  
?>