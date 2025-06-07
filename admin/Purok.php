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

    $deleteQuery = "DELETE FROM PUROK WHERE ID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
        $_SESSION['toastr'] = "toastr.success('Successfully Deleted!');";
        header("location:Purok.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete resident!');</script>";
    }
}
// DELETE DATA FROM DATABASE END


$permit_num = date("Ymd-His") . '-' . rand(10, 99);

$seleall = "SELECT * FROM PUROK ORDER BY ID DESC";
$result = $con->query($seleall) or die ($con->error);



if(isset($_POST['submit'])){
$purok = htmlspecialchars($_POST['purok']);
$details_purok = htmlspecialchars($_POST['details_purok']);

$insert = "INSERT INTO PUROK (PUROK, DETAILS) VALUES ('$purok','$details_purok')";
$query_run = mysqli_query($con,$insert);

if($query_run){
    $_SESSION['toastr'] = "toastr.success('Successfully Added new Purok!');";
    header("location:Purok.php");
    exit();
} else {
    echo "<div style='color:red; padding:10px;'>Insert failed: " . $con->error . "</div>";
}
}

// EDIT MODAL START
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $getQuery = "SELECT * FROM PUROK WHERE ID = '$edit_id'";
    $getResult = $con->query($getQuery);
    if ($getResult->num_rows > 0) {
        $editData = $getResult->fetch_assoc();
    }
}
// EDIT MODAL END

// update MODAL START
if (isset($_POST['update'])) {
  $id = $_POST['edit_id'];
  $purokupdate = $_POST['purokupdate'];
  $detailsupdate = $_POST['detailsupdate'];

  $updateSQL = "UPDATE PUROK SET PUROK = '$purokupdate', DETAILS = '$detailsupdate' WHERE ID = '$id'";
  if ($con->query($updateSQL)) {
      $_SESSION['toastr'] = "toastr.success('Successfully Updated!');";
      header("Location: Purok.php");
      exit();
  } else {
      echo "<script>alert('Update failed');</script>";
  }
}
// update MODAL END

$query = "SELECT COUNT(*) AS total FROM purok";
$resulttotal = mysqli_query($con, $query);
$rowtotal = mysqli_fetch_assoc($resulttotal); // Not $rows
$total_purok = $rowtotal['total'];
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
    Barangay Purok
    <span style="color: #0d6efd;">(<?php echo $total_purok; ?>)</span>
  </h5>
</div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#businessPermitModal">
  Add New Position
</button>
</div>


<div class="table-background">
<table class="table table-bordered custom-table table-striped" id="myTable">
    <thead>
        <tr>
            <!-- <th>RESIDENT ID</th> -->
            <th style="text-align: center;">NO.</th>
            <th style="text-align: center;">PUROK</th>
            <th style="text-align: center;">DETAILS</th>
            <th style="text-align: center;">ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
    <td style="text-align: center;"><?php echo $row['ID'] ?></td>
    <td style="text-align: center;"><?php echo $row['PUROK'] ?></td>
    <td style="text-align: center;"><?php echo $row['DETAILS'] ?></td>
    <td style="display: flex;justify-content:center;align-items:center;gap:.5rem">
      <a style="color:#d1848c" href="Purok.php?delete=<?= $row['ID'] ?>">
        <i class="fas fa-trash"></i>
      </a>
      <a href="Purok.php?edit=<?= $row['ID'] ?>" style="color:#46acc5">
  <i class="fas fa-pencil-alt"></i>
</a>

    </td>
  </tr>
<?php } ?>

    </tbody>
</table>
</div>


<!-- Business Permit ADD Modal start -->
<div class="modal fade" id="businessPermitModal" tabindex="-1" role="dialog" aria-labelledby="businessPermitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-MD" role="document">
    <div class="modal-content">
      <form action="Purok.php" method="POST">
        <div class="modal-header" style="background-color: #202b87;color:white">
          <h5 class="modal-title" id="businessPermitModalLabel">Add New Purok</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">



          <div >
            <div class="form-group col-md-12">
              <label>PUROK</label>
              <input type="text" class="form-control" name="purok" required>
            </div>
            <div class="form-group col-md-12">
              <label>DETAILS</label>
              <input type="text" class="form-control" name="details_purok" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
        <button type="submit" name="submit" class="btn btn-primary">Save</button>
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
  <div class="modal-dialog modal-MD" role="document">
    <div class="modal-content">
      <form action="Purok.php" method="POST">
        <input type="hidden" name="edit_id" value="<?= $editData['ID'] ?>">
        <div class="modal-header" style="background-color: #202b87;color:white">
          <h5 class="modal-title">Edit Barangay Purok</h5>
          <a href="Business_Permit.php" class="close"><span>&times;</span></a>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Purok</label>
            <input type="text" class="form-control" name="purokupdate" value="<?= $editData['PUROK'] ?>">
          </div>

          <div class="form-group">
            <label>Deatils</label>
            <input type="text" class="form-control" name="detailsupdate" value="<?= $editData['DETAILS'] ?>">
           
          </div> 
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-primary">Update</button>
          <a href="Purok.php" class="btn btn-secondary">Cancel</a>
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