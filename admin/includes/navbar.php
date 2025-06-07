
<?php
require_once("../connections/connection.php"); // use require_once to avoid duplicate loading
$con = connection();

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design


// --- Restore logic starts after form submission ---
if (isset($_POST['restore'])) {
    // Check if file was uploaded
    if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file.");
    }

    // Read uploaded file content
    $sqlFile = $_FILES['sql_file']['tmp_name'];
    $sqlContent = file_get_contents($sqlFile);

    if (!$sqlContent) {
        die("Failed to read SQL file.");
    }

    // Split into individual SQL statements
    $queries = array_filter(array_map('trim', explode(";", $sqlContent)));

    // Run each query
    $successCount = 0;
    $errorCount = 0;

    foreach ($queries as $query) {
        if (!empty($query)) {
            if ($con->query($query)) {
                $successCount++;
            } else {
                $errorCount++;
                echo "<p style='color:red;'>Error on query: " . $conn->error . "</p>";
            }
        }
    }

$_SESSION['toastr'] = "toastr.success('Restore complete! Successful');";
header("Location: Dashboard.php");
exit;
}

?>


<style>
@keyframes floatUpDown {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}

.floating-logo {
  animation: floatUpDown 3s ease-in-out infinite;
}

.modal-backdrop{
    z-index: 0!important;
}
.custom-file-label.selected {
    color: #495057;
    background-color: #e9ecef;
    border-color: #ced4da;
}

.modal-header {
    border-bottom: none;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

.card.border-0 {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
        <!-- Sidebar -->
<!-- Sidebar with Background Image and Gradient -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" 
    style="background: linear-gradient(180deg, rgba(37, 39, 98, 0.5) 0%, rgba(37, 39, 98, 0.4) 50%, rgba(37, 39, 98, 0.3) 100%)
, 
url('../images/brgy.jpg');
           background-size: cover;
           background-position: center;
           background-repeat: no-repeat;
           background-attachment: fixed;">

    <!-- Sidebar - Brand -->
    <div class="text-center my-4">
        <!-- Logo with Animation -->
        <img src="barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>"
            class="rounded-circle mb-2 shadow floating-logo" 
            style="width: 80px; height: 80px; object-fit: cover; background: white; padding: 5px;">
        <h5 class="text-white fw-bold mb-0" 
            style="font-weight: bold; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);">
            BRM SYSTEM
        </h5>
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="Dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Charts -->
         <li class="nav-item">
        <a class="nav-link" href="Resident_information.php">
            <i class="fas fa-user-friends"></i>
            <span>Resident information</span>
        </a>
    </li>
        <li class="nav-item">
        <a class="nav-link" href="Officials.php">
           <i class="fas fa-user-tie"></i>
            <span>Officials</span>
        </a>
    </li>
        <li class="nav-item">
        <a class="nav-link" href="Blotter_records.php">
            <i class="fas fa-book"></i>
            <span>Blotter Records</span>
        </a>
    </li>
        <li class="nav-item">
        <a class="nav-link" href="calendar.php">
           <i class="fas fa-calendar-alt"></i>
            <span>Calendar</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="BarangayCertificate.php">
           <i class="fas fa-file-alt"></i>
            <span>Barangay Certificate</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="CertificateOfIndigency.php">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Certificate Of Indigency</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="Business_Permit.php">
             <i class="fas fa-briefcase"></i>
            <span>Business Permit</span>
        </a>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="BarangayInfo.php">Barangay info</a>
                <a class="collapse-item" href="Purok.php">Purok</a>
                <a class="collapse-item" href="Precinct.php">Precinct</a>
                <div class="collapse-divider"></div>
                <a class="collapse-item" href="Position.php">Position</a>
                <a class="collapse-item" href="Chairmanship.php">Chairmanship</a>
                <a class="collapse-item" href="Users.php">Users</a>
                <a class="collapse-item" href="PendingUser.php">Pending user</a>
                <!-- Button trigger modal -->
                <a class="collapse-item" href="#" data-toggle="modal" data-target="#exampleModalbackup">Backup</a>
                <a class="collapse-item" href="#" data-bs-toggle="modal" data-bs-target="#exampleModalrestore">Restore</a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="Systemlogs.php">
            <i class="fas fa-clipboard-list"></i>
            <span>System Logs</span>
        </a>
    </li>
    
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
        <!-- End of Sidebar -->


  <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


            <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../settings/logout.php">Logout</a>
                
                </div>
            </div>
        </div>
    </div>



<!-- Modal for backup start -->
<div class="modal fade" id="exampleModalbackup" tabindex="-1" role="dialog" aria-labelledby="backupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="backupModalLabel">Database Backup</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-database fa-3x text-primary mb-3"></i>
                    <h4>Backup Your Database</h4>
                    <p class="text-muted">Create a complete backup of your database for safekeeping</p>
                </div>
                
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 text-center">
                                <i class="fas fa-info-circle text-info fa-2x"></i>
                            </div>
                            <div class="col-10">
                                <h6 class="mb-1">What this does:</h6>
                                <p class="mb-0 text-muted small">This will create a complete SQL dump of your database that you can use to restore your data later if needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <a href="backup.php" class="btn btn-success">
                    <i class="fas fa-download mr-2"></i>Download Backup Now
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal for backup end -->

<!-- Modal for restore start -->
<div class="modal fade" id="exampleModalrestore" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="restoreModalLabel">Database Restore</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-upload fa-3x text-info mb-3"></i>
                    <h4>Restore Database</h4>
                    <p class="text-muted">Upload and restore a previous database backup</p>
                </div>
                
                <!-- Warning Alert -->
                <div class="alert alert-warning" role="alert">
                    <div class="row">
                        <div class="col-2 text-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="col-10">
                            <h6 class="alert-heading mb-1">Important Warning!</h6>
                            <p class="mb-0 small">Restoring a database will completely overwrite all current data. This action cannot be undone. Make sure you have a current backup before proceeding.</p>
                        </div>
                    </div>
                </div>
                
                <!-- File Upload Form -->
                <form method="post" enctype="multipart/form-data" id="restoreForm">
                    <div class="form-group">
                        <label for="sql_file" class="font-weight-bold">
                            <i class="fas fa-file-code mr-1"></i>Select SQL Backup File:
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="sql_file" name="sql_file" required accept=".sql">
                            <label class="custom-file-label" for="sql_file">Choose SQL file...</label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>Only .sql files are accepted
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="submit" form="restoreForm" name="restore" class="btn btn-info">
                    <i class="fas fa-upload mr-2"></i>Restore Database
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for restore end -->

<!-- JavaScript for file input label and modal functionality -->
<script>
$(document).ready(function() {
    // Update custom file input label with selected filename
    $('#sql_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose SQL file...');
    });
    
    // Ensure modal close functionality works properly
    $('.modal .close, .modal .btn[data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });
    
    // Reset file input when modal is closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('input[type="file"]').val('');
        $(this).find('.custom-file-label').removeClass("selected").html('Choose SQL file...');
    });
});
</script>
