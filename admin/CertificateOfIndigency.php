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


// TO DISPLAY THE RESIDENT ON TABLE START
$selectaresident = "SELECT * FROM RESIDENT ORDER BY RESIDENT_ID DESC";
$resident = $con->query($selectaresident)or die ($con->error);
// TO DISPLAY THE RESIDENT ON TABLE END
$query = "SELECT COUNT(*) AS total FROM resident";
$resulttotal = mysqli_query($con, $query);
$rowtotal = mysqli_fetch_assoc($resulttotal); // Not $rows
$total_resident = $rowtotal['total'];
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
            <a class="dropdown-item" href="#">
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
    Certificate of Indigency
    <span style="color: #0d6efd;">(<?php echo $total_resident; ?>)</span>
  </h5>
</div>

<!-- <button class="btn btn-primary" data-toggle="modal" data-target="#addResidentModal">Add New Resident</button> -->
</div>

<div class="table-background">
<table class="table table-bordered custom-table table-striped" id="myTable">
    <thead>
        <tr>
            <!-- <th>RESIDENT ID</th> -->
            <th>FIRST NAME</th>
            <th>MIDDLE NAME</th>
            <th>LAST NAME</th>
            <th>ALIAS</th>
            <th>DATE OF BIRTH</th>
            <th>AGE</th>
            <th>STATUS</th>
            <th>GENDER</th>
            <th>PUROK</th>
            <th>VOTERS STATUS</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $resident->fetch_assoc()) { ?>
        <tr>
            <!-- <td><?php echo $row['RESIDENT_ID'] ?></td> -->
            <td><?php echo $row['FIRST_NAME'] ?></td>
            <td><?php echo $row['MIDDLE_NAME'] ?></td>
            <td><?php echo $row['LAST_NAME'] ?></td>
            <td><?php echo $row['ALIAS'] ?></td>
            <td><?php echo $row['BIRTH_DATE'] ?></td>
            <td><?php echo $row['AGE'] ?></td>
            <td><?php echo $row['CIVIL_STATUS'] ?></td> 
            <td><?php echo $row['GENDER'] ?></td>
            <td><?php echo $row['PUROK'] ?></td>
            <td><?php echo $row['VOTER_STATUS'] ?></td>
            <td style="display: flex;justify-content:Center;gap:.5rem">
            <a style="color:green" href="Indigency.php?id=<?=$row['RESIDENT_ID'] ?>" target="_blank"><i style="font-size:.9rem;color:green" class="fas fa-fw fa-file-alt"></i>GENERATE</a>
          
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>

</div>


</div>
</div>

<?php include ('includes/script.php');?>
<?php include ('includes/footer.php');?>

<?php ob_end_flush();  ?>