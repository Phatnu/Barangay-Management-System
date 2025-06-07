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

    $deleteQuery = "DELETE FROM BUSINESS_PERMIT WHERE ID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
       $_SESSION['toastr'] = "toastr.success('Successfully deleted Business permit!');";
        header("location:Business_Permit.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete resident!');</script>";
    }
}
// DELETE DATA FROM DATABASE END


$permit_num = date("Ymd-His") . '-' . rand(10, 99);

$seleall = "SELECT * FROM business_permit ORDER BY ID DESC";
$result = $con->query($seleall) or die ($con->error);



if(isset($_POST['submit'])){
$permit_num = htmlspecialchars($_POST['PERMIT_NUM']);
$resident_id = htmlspecialchars($_POST['resident_id']);
$business_name = htmlspecialchars($_POST['business_name']);
$business_type = htmlspecialchars($_POST['business_type']);
$business_add = htmlspecialchars($_POST['business_address']);
$status = htmlspecialchars($_POST['status']);
$date_app = htmlspecialchars($_POST['date_applied']);
$until_date = htmlspecialchars($_POST['valid_until']);

// Get resident's full name
$res_query = $con->query("SELECT CONCAT(first_name, ' ', last_name) AS fullname FROM resident WHERE RESIDENT_ID = '$resident_id'");
$res_data = $res_query->fetch_assoc();
$business_owner_name = $res_data['fullname'];


$insert = "INSERT INTO business_permit (PERMIT_NUM, BUSINESS_OWNER, BUSINESS_NAME, BUSINESS_TYPE, BUSINESS_ADDRESS, STATUS, DATE, VALID_UNTIL) 
VALUES ('$permit_num','$business_owner_name','$business_name','$business_type','$business_add','$status','$date_app','$until_date')";
$query_run = mysqli_query($con,$insert);

if($query_run){
     $_SESSION['toastr'] = "toastr.success('Successfully Added new Business permit!');";
    header("location:Business_Permit.php");
    exit();
} else {
    echo "<div style='color:red; padding:10px;'>Insert failed: " . $con->error . "</div>";
}
}

// EDIT MODAL START
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $getQuery = "SELECT * FROM business_permit WHERE ID = '$edit_id'";
    $getResult = $con->query($getQuery);
    if ($getResult->num_rows > 0) {
        $editData = $getResult->fetch_assoc();
    }
}
// EDIT MODAL END


// update MODAL START
if (isset($_POST['update'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['business_name'];
    $type = $_POST['business_type'];
    $address = $_POST['business_address'];
    $status = $_POST['status'];
    $date = $_POST['date_applied'];
    $valid = $_POST['valid_until'];

    $updateSQL = "UPDATE business_permit SET 
                    BUSINESS_NAME = '$name',
                    BUSINESS_TYPE = '$type',
                    BUSINESS_ADDRESS = '$address',
                    STATUS = '$status',
                    DATE = '$date',
                    VALID_UNTIL = '$valid'
                  WHERE ID = '$id'";
    if ($con->query($updateSQL)) {
       $_SESSION['toastr'] = "toastr.success('Successfully Update Business permit!');";
        header("Location: Business_Permit.php");
        exit();
    } else {
        echo "<script>alert('Update failed');</script>";
    }
}
// update MODAL END
// Query to count total female
$query = "SELECT COUNT(*) AS total FROM business_permit";
$resulttotal = mysqli_query($con, $query);
$rowtotal = mysqli_fetch_assoc($resulttotal); // Not $rows
$total_permit = $rowtotal['total'];
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
    <!-- Nav Item - User Information -->
     
    <li class="nav-item dropdown no-arrow mx-1 mt-4" style="display: flex;gap:5px">
                        <i class="fas fa-clock text-secondary me-2"></i><p id="currentTime" style="color: gray;font-size:12px"></p>
                        </li>
      <div class="topbar-divider d-none d-sm-block"></div>
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
    Business Permit Management
    <span style="color: #0d6efd;">(<?php echo $total_permit; ?>)</span>
  </h5>
</div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#businessPermitModal">
  Add Business Permit
</button>
</div>


<div class="table-background">
<table class="table table-bordered custom-table table-striped" id="myTable">
    <thead>
        <tr>
            <!-- <th>RESIDENT ID</th> -->
            <th>BUSINESS ID</th>
            <th>PERMIT NUMBER</th>
            <th>BUSINESS OWNER</th>
            <th>BUSINESS NAME</th>
            <th>BUSINESS TYPE</th>
            <th>BUSINESS ADDRESS</th>
            <th>STATUS</th>
            <th>DATE</th>
            <th>VALID UNTIL</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
    <td><?php echo $row['ID'] ?></td>
    <td><?php echo $row['PERMIT_NUM'] ?></td>
    <td><?php echo $row['BUSINESS_OWNER'] ?></td>
    <td><?php echo $row['BUSINESS_NAME'] ?></td>
    <td><?php echo $row['BUSINESS_TYPE'] ?></td>
    <td><?php echo $row['BUSINESS_ADDRESS'] ?></td>
    <td style="text-align:center">
    <?php
    // Check the status and apply the appropriate background and text color
    $status = $row['STATUS'];
    if ($status == 'Pending') {
        echo "<span style='background-color: #fff3db; color: #b88b5a; padding: 5px; border-radius: 3px;font-weight:bold'>Pending</span>";
    } elseif ($status == 'Approved') {
        echo "<span style='background-color: #dcf5dc; color: #2eaa4c; padding: 5px; border-radius: 3px;font-weight:bold'>Approved</span>";
    } elseif ($status == 'Expired') {
        echo "<span style='background-color: #f8d7da; color: #c6757f; padding: 5px; border-radius: 3px;font-weight:bold'>Expired</span>";
    }
    ?>
    </td>
    <td><?php echo $row['DATE'] ?></td>
    <td><?php echo $row['VALID_UNTIL'] ?></td>
    <td style="display: flex;justify-content:center;align-items:center;gap:.5rem">
      <a style="color:#d1848c" href="Business_Permit.php?delete=<?= $row['ID'] ?>">
        <i class="fas fa-trash"></i>
      </a>
      <a href="Business_Permit.php?edit=<?= $row['ID'] ?>" style="color:#46acc5">
  <i class="fas fa-pencil-alt"></i>
</a>

      <a style="color:#0a9b7e" href="permit.php?id=<?=$row['ID'] ?>" target="_blank">
        <i class="fas fa-fw fa-file-alt"></i>
      </a>
    </td>
  </tr>
<?php } ?>

    </tbody>
</table>
</div>


<!-- Business Permit ADD Modal start -->
<div class="modal fade" id="businessPermitModal" tabindex="-1" role="dialog" aria-labelledby="businessPermitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="Business_Permit.php" method="POST">
        <div class="modal-header" style="background-color: #202b87;color:white">
          <h5 class="modal-title" id="businessPermitModalLabel">Add Business Permit</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label>Permit Number</label>
            <input type="text" class="form-control" id="permit_num" name="PERMIT_NUM" value="<?php echo $permit_num; ?>" readonly>
          </div>

          <div class="form-group">
            <label>Business Owner</label>
            <select class="form-control" id="resident_id" name="resident_id" required>
              <option value="">-- Select Resident --</option>
              <?php
              include_once("../connections/connection.php");
              $con = connection();
              $res = $con->query("SELECT RESIDENT_ID, CONCAT(first_name, ' ', last_name) AS fullname FROM resident");
              while ($row = $res->fetch_assoc()) {
                  echo "<option value='" . $row['RESIDENT_ID'] . "'>" . $row['fullname'] . "</option>";
              }
              ?>
          </select>

          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Name of Business</label>
              <input type="text" class="form-control" name="business_name" required>
            </div>
            <div class="form-group col-md-6">
              <label>Type of Business</label>
              <input type="text" class="form-control" name="business_type" required>
            </div>
          </div>

          <div class="form-group">
            <label>Address of Business</label>
            <input type="text" class="form-control" name="business_address" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Date Applied</label>
              <input type="date" class="form-control" name="date_applied" required>
            </div>
            <div class="form-group col-md-6">
              <label>Valid Until</label>
              <input type="date" class="form-control" name="valid_until" required>
            </div>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" required>
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Expired">Expired</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-primary">Save Permit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Business Permit ADD Modal end -->

<!--edit Business Permit edit Modal start -->
<?php if ($editData): ?>
<div class="modal fade show" id="editPermitModal" tabindex="-1" role="dialog" style="display:block; background:rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="Business_Permit.php" method="POST">
        <input type="hidden" name="edit_id" value="<?= $editData['ID'] ?>">
        <div class="modal-header" style="background-color: #202b87;color:white">
          <h5 class="modal-title">Edit Business Permit</h5>
          <a href="Business_Permit.php" class="close"><span>&times;</span></a>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Permit Number</label>
            <input type="text" class="form-control" name="PERMIT_NUM" value="<?= $editData['PERMIT_NUM'] ?>" readonly>
          </div>

          <div class="form-group">
            <label>Business Owner</label>
            <input type="text" class="form-control" value="<?= $editData['BUSINESS_OWNER'] ?>" readonly>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Business Name</label>
              <input type="text" class="form-control" name="business_name" value="<?= $editData['BUSINESS_NAME'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Type</label>
              <input type="text" class="form-control" name="business_type" value="<?= $editData['BUSINESS_TYPE'] ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label>Address</label>
            <input type="text" class="form-control" name="business_address" value="<?= $editData['BUSINESS_ADDRESS'] ?>" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Date Applied</label>
              <input type="date" class="form-control" name="date_applied" value="<?= $editData['DATE'] ?>" required>
            </div>
            <div class="form-group col-md-6">
              <label>Valid Until</label>
              <input type="date" class="form-control" name="valid_until" value="<?= $editData['VALID_UNTIL'] ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" required>
              <option value="Pending" <?= $editData['STATUS'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="Approved" <?= $editData['STATUS'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
              <option value="Expired" <?= $editData['STATUS'] == 'Expired' ? 'selected' : '' ?>>Expired</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-primary">Update</button>
          <a href="Business_Permit.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<!--edit Business Permit edit Modal end -->

</div>
</div>
</div>
<?php include('includes/script.php') ?>
<?php include('includes/footer.php') ?>
<?php ob_end_flush();  ?>