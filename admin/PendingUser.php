<?php
ob_start();
session_start();
include('includes/header.php');
include('includes/navbar.php');

include_once("../connections/connection.php");

$con = connection();

if(!isset($_SESSION['STAFFID'])){
    header('location:../index.php');
    exit();
}

// TO DISPLAY THE INFO USING SESSION ID START
$user_id = $_SESSION['STAFFID'];
$sql = "SELECT * FROM STAFF WHERE STAFFID = '$user_id'";
$query = $con->query($sql);
$rows = $query->fetch_assoc();
// TO DISPLAY THE INFO USING SESSION ID START


// DELETE DATA FROM DATABASE START
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $deleteQuery = "DELETE FROM STAFF WHERE STAFFID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
      $_SESSION['toastr'] = "toastr.success('Successfully Deleted!');";
        header("location:PendingUser.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete resident!');</script>";
    }
}
// DELETE DATA FROM DATABASE END

$permit_num = date("Ymd-His") . '-' . rand(10, 99);


$seleall = "SELECT * FROM STAFF WHERE STATUS='Pending' ORDER BY STAFFID DESC";
$result = $con->query($seleall) or die ($con->error);


// EDIT MODAL START
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $getQuery = "SELECT * FROM STAFF WHERE STAFFID = '$edit_id'";
    $getResult = $con->query($getQuery);
    if ($getResult->num_rows > 0) {
        $editData = $getResult->fetch_assoc();
    }
}
// EDIT MODAL END

// update MODAL START
// UPDATE USER START
if (isset($_POST['update'])) {
  $id = $_POST['edit_id'];
  $first_name = htmlspecialchars($_POST['first_name']);
  $last_name = htmlspecialchars($_POST['last_name']);
  $birthday = $_POST['birthday'];
  $gender = $_POST['gender'];
  $email = htmlspecialchars($_POST['email']);
  $usertype = $_POST['usertype'];
  $status = $_POST['status'];

  // Handle profile image update if needed
  $profile = $_FILES['profile']['name'];
  $profile_update = "";
  
  if(!empty($profile)) {
      $target_dir = "images/";
      $target_file = $target_dir . basename($_FILES["profile"]["name"]);
      
      // Check if file is an actual image
      $check = getimagesize($_FILES["profile"]["tmp_name"]);
      if($check !== false) {
          if (move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
              $profile_update = ", PROFILE = '$profile'";
          }
      }
  }

  $updateSQL = "UPDATE STAFF SET 
                FIRST_NAME = '$first_name', 
                LAST_NAME = '$last_name', 
                BIRTH_DAY = '$birthday', 
                GENDER = '$gender', 
                EMAIL = '$email', 
                USERTYPE = '$usertype', 
                STATUS = '$status'
                $profile_update
                WHERE STAFFID = '$id'";
  
  if ($con->query($updateSQL)) {
      $_SESSION['toastr'] = "toastr.success('Successfully Updated!');";
      header("Location: PendingUser.php");
      exit();
  } else {
      echo "<script>alert('Update failed: " . $con->error . "');</script>";
  }
}
// UPDATE USER END
// update MODAL END


$query = "SELECT COUNT(*) AS total FROM STAFF WHERE STATUS='Pending'";
$resulttotal = mysqli_query($con, $query);
$rowtotal = mysqli_fetch_assoc($resulttotal); // Not $rows
$total_pending = $rowtotal['total'];
?>
<style>
        /* Table Styling */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Table Header */
        .custom-table thead {
            background-color: #414a49; /* Match the brownish header */
            color: white;
            font-size: 12px; /* Adjust text size */
            font-weight: bold;
            text-align: left;
        }

        .custom-table thead th {
            padding: 12px;
            border-bottom: 2px solid #ddd;
        }

        /* Table Rows */
        .custom-table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .custom-table tbody td {
            padding: 10px;
            font-size: 13px; /* Adjust text size */
        }

        /* Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #dadada;
            color: white !important;
            margin: 2px;
            padding: 6px 12px;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #5f5b3b;
            color:white; /* Darker highlight */
        }
        .table-background{
            padding:1rem;-webkit-box-shadow: -1px 2px 10px -1px rgba(153,151,153,1);
-moz-box-shadow: -1px 2px 10px -1px rgba(153,151,153,1);
box-shadow: -1px 2px 10px -1px rgba(153,151,153,1);
        }
        
    </style>


<div id="content-wrapper" class="d-flex flex-column">
           
 <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
</button>

<!-- Topbar Navbar -->
<ul class="navbar-nav ml-auto">
  
<li class="nav-item dropdown no-arrow mx-1 mt-4" style="display: flex;gap:5px">
                        <i class="fas fa-clock text-secondary me-2"></i><p id="currentTime" style="color: gray;font-size:12px"></p>
                        </li>
      <div class="topbar-divider d-none d-sm-block"></div>
    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$rows['FIRST_NAME']?></label></span>
            <img class="img-profile rounded-circle"
                src="images/<?=$rows['PROFILE']?>">
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="userDropdown">
            <a class="dropdown-item" href="profile.php">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
            <a class="dropdown-item" href="../admin/change_password.php">
                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                Account Settings
            </a>
             <a class="dropdown-item" href="Systemlogs.php">
                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                Activity Log
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
<!-- End of Topbar -->
<div style="padding: 20px;">

<div style="box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-webkit-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-moz-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);border-radius: 5px;; color: black; padding: 10px;display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;border-radius:5px">
<div style="border-left: 5px solid #0d6efd; padding-left: 10px;">
  <h5 style="margin: 0; font-weight: 600; color: #333;">
    Pending User List
    <span style="color: #0d6efd;">(<?php echo $total_pending; ?>)</span>
  </h5>
</div>

</div>


<div class="table-background">
<table class="table table-bordered custom-table table-striped" id="myTable">
    <thead>
        <tr>
        <th style="text-align: center;">PROFILE</th>
            <th style="text-align: center;display:none">ID</th>
            <th style="text-align: center;">FIRST NAME</th>
            <th style="text-align: center;">LAST NAME</th>
            <th style="text-align: center;">BIRTHDAY</th>
            <th style="text-align: center;">GENDER</th>
            <th style="text-align: center;">EMAIL</th>
            <th style="text-align: center;">USERTYPE</th>
            <th style="text-align: center;">STATUS</th>
            <th style="text-align: center;">ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
  <td style="text-align: center;">
        <img src="images/<?php echo $row['PROFILE'] ?>" alt="" width="40" height="40" style="object-fit: cover; border-radius: 50%;border:1px solid #35366c">
    </td>
    <td style="text-align: center;display:none"><?php echo $row['STAFFID'] ?></td>
    <td style="text-align: center;"><?php echo $row['FIRST_NAME'] ?></td>
    <td style="text-align: center;"><?php echo $row['LAST_NAME'] ?></td>
    <td style="text-align: center;"><?php echo $row['BIRTH_DAY'] ?></td>
    <td style="text-align: center;"><?php echo $row['GENDER'] ?></td>
    <td style="text-align: center;"><?php echo $row['EMAIL'] ?></td>
    <td style="text-align: center;"><?php echo $row['USERTYPE'] ?></td>
    <td style="text-align: center;"><?php echo $row['STATUS'] ?></td>

    <td style="display: flex;justify-content:center;align-items:center;gap:.5rem">
      <a style="color:#d1848c" href="PendingUser.php?delete=<?= $row['STAFFID'] ?>">
        <i class="fas fa-trash"></i>
      </a>
      <a href="PendingUser.php?edit=<?= $row['STAFFID'] ?>" style="color:#46acc5">
  <i class="fas fa-pencil-alt"></i>
</a>

    </td>
  </tr>
<?php } ?>

    </tbody>
</table>
</div>


<!-- Edit User Modal -->
<?php if ($editData): ?>
<div class="modal fade show" id="editUserModal" tabindex="-1" role="dialog" style="display:block; background:rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="PendingUser.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $editData['STAFFID'] ?>">
        <div class="modal-header" style="background-color: #202b87;color:white">
          <h5 class="modal-title">Edit User</h5>
          <a href="PendingUser.php" class="close"><span>&times;</span></a>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>First Name</label>
              <input type="text" class="form-control" name="first_name" value="<?= $editData['FIRST_NAME'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Last Name</label>
              <input type="text" class="form-control" name="last_name" value="<?= $editData['LAST_NAME'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Birthday</label>
              <input type="date" class="form-control" name="birthday" value="<?= $editData['BIRTH_DAY'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Gender</label>
              <select class="form-control" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?= ($editData['GENDER'] == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($editData['GENDER'] == 'Female') ? 'selected' : '' ?>>Female</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" name="email" value="<?= $editData['EMAIL'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>User Type</label>
              <select class="form-control" name="usertype" required>
                <option value="">Select User Type</option>
                <option value="Admin" <?= ($editData['USERTYPE'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                <option value="Staff" <?= ($editData['USERTYPE'] == 'Staff') ? 'selected' : '' ?>>Staff</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Status</label>
              <select class="form-control" name="status" required>
                <option value="">Select Status</option>
                <option value="Active" <?= ($editData['STATUS'] == 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= ($editData['STATUS'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Current Profile Picture</label><br>
              <img src="images/<?= $editData['PROFILE'] ?>" alt="Profile" width="100" height="100" style="object-fit: cover; border-radius: 50%;">
            </div>
            <div class="form-group col-md-12">
              <label>Update Profile Picture (Leave blank to keep current image)</label>
              <input type="file" class="form-control" name="profile">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-primary">Update</button>
          <a href="PendingUser.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<!-- Edit User Modal end -->

</div>
</div>
</div>
<?php include('includes/script.php') ?>
<?php include('includes/footer.php') ?>
<?php ob_end_flush();  ?>