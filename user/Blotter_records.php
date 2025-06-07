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
$selectaresident = "SELECT * FROM BLOTTER2 ORDER BY ID DESC";
$resident = $con->query($selectaresident)or die ($con->error);

// TO DISPLAY THE RESIDENT ON TABLE END


// Save new blotter start
if (isset($_POST['btnsave'])) {

    $incidenttype = htmlspecialchars($_POST['incidenttype']);
    $complainant_name = htmlspecialchars($_POST['complainant_name']);
    $respondent_name = htmlspecialchars($_POST['respondent_name']);
    $victims_name = htmlspecialchars($_POST['victims_name']);
    $incident_date = htmlspecialchars($_POST['incident_date']);
    $incident_time = htmlspecialchars($_POST['incident_time']);
    $incident_location = htmlspecialchars($_POST['incident_location']);
    $status = htmlspecialchars($_POST['status']);
    $date_of_schedule = htmlspecialchars($_POST['date_of_schedule']);
    $time_schedule = htmlspecialchars($_POST['time_schedule']);
    $description = htmlspecialchars($_POST['description']);

    // Insert blotter record
    $insert = "INSERT INTO BLOTTER2 (
        INCIDENT_TYPE,
        COMPLAINANT_NAME,
        RESPONDENT,
        VICTIM,
        INCIDENT_DATE,
        INCIDENT_TIME,
        INCIDENT_LOCATION,
        STATUS,
        INCIDENT_DESCRIPTION,
        DATE_SCHEDULE,
        TIME_SCHEDULE
    ) VALUES (
        '$incidenttype',
        '$complainant_name',
        '$respondent_name',
        '$victims_name',
        '$incident_date',
        '$incident_time',
        '$incident_location',
        '$status',
        '$description',
        '$date_of_schedule',
        '$time_schedule'
    )";

    $query_run = mysqli_query($con, $insert);

    if ($query_run) {
        // Get the last inserted blotter ID
        $blotterId = mysqli_insert_id($con);

        // Log the activity
        if (isset($_SESSION['STAFFID'])) {
            $staffId = mysqli_real_escape_string($con, $_SESSION['STAFFID']);
            $action = mysqli_real_escape_string($con, "Add");
            $targetTable = mysqli_real_escape_string($con, "BLOTTER2");
            $targetId = mysqli_real_escape_string($con, $blotterId);
            $targetName = mysqli_real_escape_string($con, $complainant_name);
            $logDescription = mysqli_real_escape_string($con, "Added new blotter record for complainant: $complainant_name (Blotter ID: $blotterId)");

            $logQuery = "INSERT INTO activity_log (
                STAFFID, ACTION_TYPE, ACTION_DESCRIPTION, TARGET_TABLE, TARGET_ID, TARGET_NAME
            ) VALUES (
                '$staffId', '$action', '$logDescription', '$targetTable', '$targetId', '$targetName'
            )";

            if (!$con->query($logQuery)) {
                echo "Activity log error: " . $con->error;
            }
        } else {
            echo "STAFFID session not set. Activity not logged.";
        }

        header("Location: Blotter_records.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
// Save new blotter end



// Query to count total active cases
$query = "SELECT COUNT(*) AS total FROM BLOTTER2 WHERE STATUS = 'Active'";
$result = mysqli_query($con, $query);
$rows1 = mysqli_fetch_assoc($result); // Not $rows
$active_cases = $rows1['total'];

// Query to count total schedule
$query = "SELECT COUNT(*) AS total FROM BLOTTER2 WHERE STATUS = 'Scheduled'";
$result = mysqli_query($con, $query);
$rows1 = mysqli_fetch_assoc($result); // Not $rows
$scheduled = $rows1['total'];

// Query to count total settled
$query = "SELECT COUNT(*) AS total FROM BLOTTER2 WHERE STATUS = 'Settled'";
$result = mysqli_query($con, $query);
$rows1 = mysqli_fetch_assoc($result); // Not $rows
$Settled = $rows1['total'];

// DELETE DATA FROM DATABASE START
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get complainant name before deleting (for logging)
    $complainantName = 'Unknown';
    $getBlotter = "SELECT COMPLAINANT_NAME FROM BLOTTER2 WHERE ID = '$id'";
    $resultGet = $con->query($getBlotter);
    if ($resultGet->num_rows > 0) {
        $data = $resultGet->fetch_assoc();
        $complainantName = $data['COMPLAINANT_NAME'];
    }

    // Perform delete
    $deleteQuery = "DELETE FROM BLOTTER2 WHERE ID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
        // Insert into activity_log
        if (isset($_SESSION['STAFFID'])) {
            $staffId = mysqli_real_escape_string($con, $_SESSION['STAFFID']);
            $action = mysqli_real_escape_string($con, "Delete");
            $description = mysqli_real_escape_string($con, "Deleted blotter record: $complainantName (ID: $id)");
            $targetTable = mysqli_real_escape_string($con, "BLOTTER2");
            $targetId = mysqli_real_escape_string($con, $id);
            $targetName = mysqli_real_escape_string($con, $complainantName);

            $logQuery = "INSERT INTO activity_log 
                (STAFFID, ACTION_TYPE, ACTION_DESCRIPTION, TARGET_TABLE, TARGET_ID, TARGET_NAME) 
                VALUES 
                ('$staffId', '$action', '$description', '$targetTable', '$targetId', '$targetName')";

            if (!$con->query($logQuery)) {
                echo "Activity log error: " . $con->error;
            }
        } else {
            echo "STAFFID session not set. Activity not logged.";
        }

        header("location:Blotter_records.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete blotter record!');</script>";
    }
}
// DELETE DATA FROM DATABASE END

// EDIT MODAL START
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $getQuery = "SELECT * FROM BLOTTER2 WHERE ID = '$edit_id'";
    $getResult = $con->query($getQuery);
    if ($getResult->num_rows > 0) {
        $editData = $getResult->fetch_assoc();
    }
}
// EDIT MODAL END

// UPDATE BLOTTER RECORD START
if (isset($_POST['update'])) {

    // Sanitize and fetch form data
    $id = mysqli_real_escape_string($con, $_POST['edit_id']);
    $incident_type = mysqli_real_escape_string($con, $_POST['incidenttype']);
    $complainant = mysqli_real_escape_string($con, $_POST['complainant_name']);
    $respondent = mysqli_real_escape_string($con, $_POST['respondent_name']);
    $victim = mysqli_real_escape_string($con, $_POST['victims_name']);
    $incident_date = mysqli_real_escape_string($con, $_POST['incident_date']);
    $incident_time = mysqli_real_escape_string($con, $_POST['incident_time']);
    $location = mysqli_real_escape_string($con, $_POST['incident_location']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // Optional: Initialize schedule values as NULL
    $schedule_date = $schedule_time = 'NULL';

    // If status is Scheduled, get schedule fields
    if ($status === 'Scheduled') {
        $schedule_date = "'" . mysqli_real_escape_string($con, $_POST['date_of_schedule2']) . "'";
        $schedule_time = "'" . mysqli_real_escape_string($con, $_POST['time_schedule2']) . "'";
    }

    // Update query
    $updateQuery = "
        UPDATE BLOTTER2 SET
            INCIDENT_TYPE = '$incident_type',
            COMPLAINANT_NAME = '$complainant',
            RESPONDENT = '$respondent',
            VICTIM = '$victim',
            INCIDENT_DATE = '$incident_date',
            INCIDENT_TIME = '$incident_time',
            INCIDENT_LOCATION = '$location',
            STATUS = '$status',
            DATE_SCHEDULE = $schedule_date,
            TIME_SCHEDULE = $schedule_time,
            INCIDENT_DESCRIPTION = '$description'
        WHERE ID = '$id'
    ";

    if (mysqli_query($con, $updateQuery)) {
        // Log activity
        if (isset($_SESSION['STAFFID'])) {
            $staffId = mysqli_real_escape_string($con, $_SESSION['STAFFID']);
            $action = "Update";
            $descriptionLog = "Updated blotter record for complainant: $complainant (ID: $id)";
            $targetTable = "BLOTTER2";
            $targetId = $id;
            $targetName = $complainant;

            $logQuery = "INSERT INTO activity_log (
                STAFFID, ACTION_TYPE, ACTION_DESCRIPTION, TARGET_TABLE, TARGET_ID, TARGET_NAME
            ) VALUES (
                '$staffId', '$action', '$descriptionLog', '$targetTable', '$targetId', '$targetName'
            )";

            if (!$con->query($logQuery)) {
                echo "Activity log error: " . $con->error;
            }
        } else {
            echo "STAFFID session not set. Activity not logged.";
        }

        header("Location: Blotter_records.php");
        exit();
    } else {
        echo $con->error;
    }
}
// UPDATE BLOTTER RECORD END


$query = "SELECT COUNT(*) AS total FROM blotter2";
$resulttotal = mysqli_query($con, $query);
$rowtotal = mysqli_fetch_assoc($resulttotal); // Not $rows
$total_blotter = $rowtotal['total'];



?>

<style>
    /* New Stats Card Styling */
  .stats-container {
    margin-bottom: 1rem;
  }
  
  .stats-card-new {
    border-radius: 12px;
    height: 120px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 1.5rem;
    transition: all 0.3s ease;
  }
  
  .stats-card-new:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
  }
  
  .stats-card-new::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 50%);
    z-index: 1;
  }
  
  .stats-card-new .icon {
    position: absolute;
    top: 50%;
    right: 1.5rem;
    transform: translateY(-50%);
    font-size: 3.5rem;
    opacity: 0.2;
    z-index: 0;
  }
  
  .stats-card-new .label {
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    z-index: 2;
    position: relative;
  }
  
  .stats-card-new .count {
    font-size: 2.5rem;
    font-weight: 700;
    z-index: 2;
    position: relative;
  }
  
  .active-card-new {
    background: linear-gradient(150deg, #ff5f6d 0%, #ff416c 100%);
    color: white;
  }
  
  .scheduled-card-new {
    background: linear-gradient(150deg, #ffb347 0%, #ff8c00 100%);
    color: white;
  }
  
  .settled-card-new {
    background: linear-gradient(150deg, #00c9a7 0%, #0fd082 100%);
    color: white;
  }
  
  .rotate-icon {
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0% {
      transform: translateY(-50%) scale(1);
    }
    50% {
      transform: translateY(-50%) scale(1.1);
    }
    100% {
      transform: translateY(-50%) scale(1);
    }
  }
  
  /* When hovering, make the icon more visible */
  .stats-card-new:hover .icon {
    opacity: 0.4;
  }
  
  /* Badge indicator */
  .badge-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: white;
    animation: blink 1.5s infinite;
    z-index: 2;
  }
  
  @keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.3; }
    100% { opacity: 1; }
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .stats-card-new {
      height: 100px;
      margin-bottom: 1rem;
    }
    
    .stats-card-new .count {
      font-size: 2rem;
    }
    
    .stats-card-new .icon {
      font-size: 2.5rem;
    }
  }
  .dashboard-container {
   
    background-color: #f8f9fc;
  }
  
  .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    margin-bottom: 1.5rem;
  }
  
  .card:hover {
    transform: translateY(-5px);
  }
  
  .card-header {
    background-color: #fff;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .status-badge {
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  
  .status-active {
    background-color: #ff6b6b;
    color: white;
  }
  
  .status-scheduled {
    background-color: #ffd166;
    color: #212529;
  }
  
  .status-settled {
    background-color: #2ec4b6;
    color: white;
  }
  
  .stats-card {
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
  }
  
  .stats-card i {
    font-size: 2rem;
    opacity: 0.8;
  }
  
  .stats-card .count {
    font-size: 2rem;
    font-weight: 700;
  }
  
  .stats-card .label {
    font-size: 1rem;
    opacity: 0.9;
  }
  
  .active-card {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
  }
  
  .settled-card {
    background: linear-gradient(45deg, #11998e, #38ef7d);
  }
  
  .scheduled-card {
    background: linear-gradient(45deg, #f7b733, #fc4a1a);
  }
  
  .action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    margin-right: 0.25rem;
  }
  
  .btn-add-blotter {
    background-color: #4e73df;
    color: white;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s;
  }
  
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
  
  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
  
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  ::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
  /* Force modal vertically centered */
.modal.show.d-block {

  overflow-y: auto;
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
  <img class="img-profile rounded-circle" src="../admin/images/<?=$rows['PROFILE']?>">
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
<!-- End of Topbar -->
<div style="padding:20px">
<div class="dashboard-container">
    <div class="row stats-container">
      <!-- Redesigned Stats Cards Section -->
      <div class="col-xl-12">
        <div class="row">
          <div class="col-md-4">
            <div class="stats-card-new active-card-new">
              <div class="badge-indicator"></div>
              <div class="label">Active Cases</div>
              <div class="count"><?php echo $active_cases; ?></div>
              <i class="fas fa-exclamation-circle icon rotate-icon"></i>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card-new scheduled-card-new">
              <div class="label">Scheduled Cases</div>
              <div class="count"><?php echo $scheduled; ?></div>
              <i class="fas fa-calendar-alt icon"></i>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card-new settled-card-new">
              <div class="label">Settled Cases</div>
              <div class="count"><?php echo $Settled; ?></div>
              <i class="fas fa-check-circle icon"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Blotter Table Section -->
      <div style="box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-webkit-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-moz-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);border-radius: 5px;; color: black; padding: 10px;display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;width:100%;border-radius:5px">
      <div style="border-left: 5px solid #0d6efd; padding-left: 10px;">
  <h5 style="margin: 0; font-weight: 600; color: #333;">
    Blotter/Incedent Complaint
    <span style="color: #0d6efd;">(<?php echo $total_blotter; ?>)</span>
  </h5>
</div>
      <button class="btn btn-primary" data-toggle="modal" data-target="#addBlotterModal">Add Blotter Records</button>
<!-- <button class="btn btn-primary" data-toggle="modal" data-target="#addResidentModal">Add New Resident</button> -->
</div>

<div class="table-background w-100">
<table class="table table-bordered custom-table table-striped" id="myTable">
    <thead>
        <tr>
            <!-- <th>RESIDENT ID</th> -->
            <th style="display:none;">ID</th>
            <th>INCIDENT TYPE</th>
            <th>COMPLAINANT NAME</th>
            <th>RESPONDENT</th>
            <th>VICTIM</th>
            <th>INCIDENT DATE</th>
            <th>TIME</th>
            <th>LOCATION</th>
            <th>STATUS</th>
            <th>DESCRIPTION</th>
            <th style="display:none;">DATE SCHEDULE</th>
            <th style="display:none;">TIME SCHEDULE</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $resident->fetch_assoc()) { ?>
        <tr>
            <td style="display:none;"><?php echo $row['ID'] ?></td>
            <td><?php echo $row['INCIDENT_TYPE'] ?></td>
            <td><?php echo $row['COMPLAINANT_NAME'] ?></td>
            <td><?php echo $row['RESPONDENT'] ?></td>
            <td><?php echo $row['VICTIM'] ?></td>
            <td><?php echo $row['INCIDENT_DATE'] ?></td>
            <td><?php echo $row['INCIDENT_TIME'] ?></td>
            <td><?php echo $row['INCIDENT_LOCATION'] ?></td> 
            <td style="text-align:center">
            <?php
            // Check the status and apply the appropriate background and text color
            $status = $row['STATUS'];
            if ($status == 'Active') {
                echo "<span style='background-color: #dcf5dc; color: #2eaa4c;padding: 5px; border-radius: 3px;font-weight:bold'>Active</span>";
            } elseif ($status == 'Scheduled') {
                echo "<span style='background-color: #fff3db; color: #b88b5a; padding: 5px; border-radius: 3px;font-weight:bold'>Scheduled</span>";
            } elseif ($status == 'Settled') {
                echo "<span style='background-color: #d5effe; color: #7895a5; padding: 5px; border-radius: 3px;font-weight:bold'>Settled</span>";
            }
            ?>
            </td>
            <td><?php echo $row['INCIDENT_DESCRIPTION'] ?></td>
            <td style="display:none;"><?php echo $row['DATE_SCHEDULE'] ?></td>
            <td style="display:none;"><?php echo $row['TIME_SCHEDULE'] ?></td>
            <td style="display: flex;justify-content:center;align-items:center;gap:.5rem">
      <a style="color:gray" href="Blotter_records.php?delete=<?= $row['ID'] ?>">
        <i class="fas fa-trash"></i>
      </a>
      <a href="Blotter_records.php?edit=<?= $row['ID'] ?>" style="color:gray">
  <i class="fas fa-pencil-alt"></i>
</a>

      <a style="color:gray" href="Blotter.php?id=<?=$row['ID'] ?>" target="_blank">
        <i class="fas fa-fw fa-file-alt"></i>
      </a>
    </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>
    </div>
  </div>
</div>


<!-- Add Blotter Modal -->
<div class="modal fade" id="addBlotterModal" tabindex="-1" role="dialog" aria-labelledby="addBlotterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #202b87;">
        <h5 class="modal-title" id="addBlotterModalLabel">Add New Blotter/Incident</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="blotterForm" method="POST" action="Blotter_records.php">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="incidentType">Incident Type</label>
                <select class="form-control" id="incidentType" required name="incidenttype">
                  <option value="">Select Incident Type</option>
                  <option value="Incident">Incident</option>
                  <option value="Amicable">Amicable</option>
                  <option value="Dispute">Dispute</option>
                </select>
              </div>
              <div class="form-group">
                <label for="complainant">Complainant</label>
                <input type="text" class="form-control" id="complainant" name="complainant_name" placeholder="Enter complainant name" required>
              </div>
              <div class="form-group">
                <label for="respondent">Respondent</label>
                <input type="text" class="form-control" id="respondent" name="respondent_name" placeholder="Enter respondent name" required>
              </div>
              <div class="form-group">
                <label for="victim">Victim(s)</label>
                <input type="text" class="form-control" id="victim" name="victims_name" placeholder="Enter victim name(s)">
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="incidentDate">Incident Date</label>
                    <input type="date" class="form-control" id="incidentDate" name="incident_date" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="incidentTime">Incident Time</label>
                    <input type="time" class="form-control" id="incidentTime" name="incident_time" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">

              <div class="form-group">
                <label for="incidentLocation">Incident Location</label>
                <input type="text" class="form-control" id="incidentLocation" name="incident_location" placeholder="Enter incident location" required>
              </div>
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="status" required>
                  <option value="Active">Active</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="Settled">Settled</option>
                </select>
              </div>

              <!-- Conditionally visible schedule fields -->
              <div id="scheduleFields" style="display: none;">
                <div class="form-group">
                  <label for="scheduleDate">Date of Schedule</label>
                  <input type="date" name="date_of_schedule" class="form-control" id="scheduleDate">
                </div>
                <div class="form-group">
                  <label for="scheduleTime">Time of Schedule</label>
                  <input type="time" class="form-control" name="time_schedule" id="scheduleTime">
                </div>
              </div>

              <div class="form-group">
                <label for="description">Incident Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter incident description" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" name="btnsave" class="btn btn-primary">Submit</button>

      </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Add Blotter Modal End -->

<!-- Edit Blotter Modal Start -->
<?php if ($editData): ?>
<div class="modal fade show d-block" id="editBlotterModal" tabindex="-1" role="dialog" style="background:rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg" role="document">
    <form action="Blotter_records.php" method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="edit_id" value="<?= $editData['ID'] ?>">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header text-white" style="background-color: #202b87;">
          <h5 class="modal-title fw-bold">Edit Blotter Record</h5>
          <a href="Blotter_Records.php" class="btn-close btn-close-white" aria-label="Close"></a>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Incident Type</label>
              <input type="text" class="form-control" name="incidenttype" value="<?= $editData['INCIDENT_TYPE'] ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Complainant</label>
              <input type="text" class="form-control" name="complainant_name" value="<?= $editData['COMPLAINANT_NAME'] ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Respondent</label>
              <input type="text" class="form-control" name="respondent_name" value="<?= $editData['RESPONDENT'] ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Victim</label>
              <input type="text" class="form-control" name="victims_name" value="<?= $editData['VICTIM'] ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="incident_date" value="<?= $editData['INCIDENT_DATE'] ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Time</label>
              <input type="time" class="form-control" name="incident_time" value="<?= $editData['INCIDENT_TIME'] ?>" required>
            </div>
            <div class="col-md-12">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="incident_location" value="<?= $editData['INCIDENT_LOCATION'] ?>" required>
            </div>
            <div class="col-md-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4"><?= $editData['INCIDENT_DESCRIPTION'] ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-control" id="status2" required>
                <option value="Active" <?= $editData['STATUS'] == 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Scheduled" <?= $editData['STATUS'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                <option value="Settled" <?= $editData['STATUS'] == 'Settled' ? 'selected' : '' ?>>Settled</option>
              </select>
            </div>
            <!-- Schedule Fields -->
            <div id="scheduleFields2" class="col-md-6" style="display: none;">
              <label class="form-label">Date of Schedule</label>
              <input type="date" name="date_of_schedule2" class="form-control" id="scheduleDate2" value="<?= $editData['DATE_SCHEDULE'] ?>">
              <label class="form-label mt-2">Time of Schedule</label>
              <input type="time" class="form-control" name="time_schedule2" id="scheduleTime2" value="<?= $editData['TIME_SCHEDULE'] ?>">
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex">
        <a href="Blotter_Records.php" class="btn btn-danger">Cancel</a>
          <button type="submit" name="update" class="btn btn-primary">Update Record</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Edit Blotter Modal End -->


</div>
</div>

</div>

<!-- Script to show/hide schedule fields for schedule -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const statusDropdown = document.getElementById("status");
    const scheduleFields = document.getElementById("scheduleFields");
    const scheduleDate = document.getElementById("scheduleDate");
    const scheduleTime = document.getElementById("scheduleTime");

    statusDropdown.addEventListener("change", function () {
      if (this.value === "Scheduled") {
        scheduleFields.style.display = "block";
        scheduleDate.setAttribute("required", "required");
        scheduleTime.setAttribute("required", "required");
      } else {
        scheduleFields.style.display = "none";
        scheduleDate.removeAttribute("required");
        scheduleTime.removeAttribute("required");
      }
    });
  });
</script>






<!-- Script to show/hide schedule fields for Scheduled status -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const statusDropdown = document.getElementById("status2");
    const scheduleFields = document.getElementById("scheduleFields2");
    const scheduleDate = document.getElementById("scheduleDate2");
    const scheduleTime = document.getElementById("scheduleTime2");

    function toggleScheduleFields() {
      if (statusDropdown.value === "Scheduled") {
        scheduleFields.style.display = "block";
        scheduleDate.setAttribute("required", "required");
        scheduleTime.setAttribute("required", "required");
      } else {
        scheduleFields.style.display = "none";
        scheduleDate.removeAttribute("required");
        scheduleTime.removeAttribute("required");
      }
    }

    // Call on change
    statusDropdown.addEventListener("change", toggleScheduleFields);

    // Call on page load to reflect initial status
    toggleScheduleFields();
  });
</script>



<?php include ('includes/script.php');?>
<?php include ('includes/footer.php');?>

<?php ob_end_flush();  ?>

