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
$rows = $query->fetch_assoc();
// TO DISPLAY THE INFO USING SESSION ID START


$id = $_GET['Resident'];

$SelectResident = "SELECT * FROM RESIDENT WHERE RESIDENT_ID = '$id'";
$result = $con->query($SelectResident) or die ($con->error);
$row = $result->fetch_assoc();

// UPDATE FUNCTION START
if (isset($_POST['update_resident'])) {
    $restid = $_POST['restid'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $alias = $_POST['alias'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $age = $_POST['age'];
    $civil_status = $_POST['civil_status'];
    $place_of_birth = $_POST['place_of_birth'];
    $voter_status = $_POST['voter_status'];
    $phone = $_POST['phone'];
    $purok = $_POST['purok'];
    $email = $_POST['email'];

    // Define upload directory
    $upload_dir = '../admin/Profile/';

    // Handle profile photo
    if (!empty($_FILES['profile']['name'])) {
        $filename = basename($_FILES['profile']['name']);
        $profile = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['profile']['tmp_name'], $profile)) {
            echo "Error uploading file.";
            exit();
        }

        // Save only file name to DB
        $profile = $filename;
    } else {
        // Use existing file name
        $profile = mysqli_real_escape_string($con, $_POST['existing_profile']);
    }

    // Update query
    $updatequery = "UPDATE RESIDENT SET 
        FIRST_NAME='$first_name',
        MIDDLE_NAME='$middle_name',
        LAST_NAME='$last_name',
        ALIAS='$alias',
        PLACE_OF_BIRTH='$place_of_birth',
        BIRTH_DATE='$birth_date',
        AGE='$age',
        CIVIL_STATUS='$civil_status',
        GENDER='$gender',
        PUROK='$purok',
        VOTER_STATUS='$voter_status',
        PHONE='$phone',
        EMAIL='$email',
        PROFILE='$profile'
        WHERE RESIDENT_ID = '$restid'";

    $query_run = mysqli_query($con, $updatequery);

    if ($query_run) {
        // Log action to activity_log
        if (isset($_SESSION['STAFFID'])) {
            $residentName = $first_name . ' ' . $last_name;
            $staffId = mysqli_real_escape_string($con, $_SESSION['STAFFID']);
            $action = mysqli_real_escape_string($con, "Update");
            $description = mysqli_real_escape_string($con, "Updated resident: $residentName (ID: $restid)");
            $targetTable = mysqli_real_escape_string($con, "RESIDENT");
            $targetId = mysqli_real_escape_string($con, $restid);
            $targetName = mysqli_real_escape_string($con, $residentName);

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

        header("Location: Resident_view.php?Resident=" . $restid);
        exit();
    } else {
        echo $con->connect_error;
    }
}
// UPDATE FUNCTION END
?>

<style>
    .profile-img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #f8f9fa;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .profile-sidebar {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        height: fit-content;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
    }
    
    .info-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border-bottom: 2px solid #e3e6f0;
    }
    
    .form-label {
        font-weight: 600;
        color: #4e73df;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    
    .form-control:read-only {
        background-color: #f8f9fa;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 8px;
        color: #4e73df;
    }
    
    .profile-badge {
        background-color: #4e73df;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        display: inline-block;
        margin-top: 5px;
    }



    /* PROFILE AND CAMERA FROM MODAL START */
    .profile-container {
    position: relative;
    display: inline-block;
}

.camera-button {
    position: absolute;
    right: -15px;
    bottom: 15px;
    background-color: #f0f0f0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #333;
    font-size: 16px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}

.camera-button:hover {
    background-color: #e0e0e0;
}
    /* PROFILE AND CAMERA FROM MODAL END */
</style>

<div id="content-wrapper" class="d-flex flex-column">
           
        
            <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

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

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-header text-white py-3" style="background-color: #202b85;">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-user-circle mr-2"></i> Resident Information</h5>
                    <div>
                        <button class="btn btn-warning btn-sm mr-2" data-toggle="modal" data-target="#editResidentModal">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <a href="dashboard.php" class="btn btn-sm btn-light"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
                    </div>                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Profile Sidebar - LEFT SIDE -->
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="profile-sidebar mb-4">
                            <div class="mb-4">
                                <img src="../admin/Profile/<?=$row['PROFILE']?>" alt="Profile Picture" class="profile-img mb-3">
                            </div>
                            <h4><?= htmlspecialchars($row['FIRST_NAME']) ?> <?= htmlspecialchars($row['LAST_NAME']) ?></h4>
                            <div class="profile-badge">
                                ID: <?= htmlspecialchars($row['RESIDENT_ID']) ?>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-left">
                                <p><strong>Civil Status:</strong> <?= htmlspecialchars($row['CIVIL_STATUS']) ?></p>
                                <p><strong>Gender:</strong> <?= htmlspecialchars($row['GENDER']) ?></p>
                                <p><strong>Age:</strong> <?= htmlspecialchars($row['AGE']) ?></p>
                                <p><strong>Voter Status:</strong> <?= htmlspecialchars($row['VOTER_STATUS']) ?></p>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-center">
                                <a href="#" class="btn btn-primary btn-block"><i class="fas fa-print mr-1"></i> Print Information</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content - RIGHT SIDE -->
                    <div class="col-lg-9 col-md-8">
                        <!-- Personal Information -->
                        <div class="info-section">
                            <h5 class="section-title"><i class="fas fa-user text-primary"></i> Personal Information</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['FIRST_NAME']) ?>" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['MIDDLE_NAME']) ?>" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['LAST_NAME']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alias</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['ALIAS']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['GENDER']) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Birth Information -->
                        <div class="info-section">
                            <h5 class="section-title"><i class="fas fa-calendar-alt text-primary"></i> Birth Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Birth Date</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['BIRTH_DATE']) ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Age</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['AGE']) ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Civil Status</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['CIVIL_STATUS']) ?>" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Place of Birth</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['PLACE_OF_BIRTH']) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address & Contact -->
                        <div class="info-section">
                            <h5 class="section-title"><i class="fas fa-address-card text-primary"></i> Address & Contact</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Purok</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['PUROK']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Voter Status</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['VOTER_STATUS']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['PHONE']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['EMAIL']) ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ID Information -->
                        <div class="info-section">
                            <h5 class="section-title"><i class="fas fa-id-card text-primary"></i> ID Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Resident ID</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['RESIDENT_ID']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Official ID</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['OFFICIAL_ID']) ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            





  <!-- Edit Resident Modal start-->
<div class="modal fade" id="editResidentModal" tabindex="-1" role="dialog" aria-labelledby="editResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editResidentModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Resident Information
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="Resident_view.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Navigation tabs -->
                    <ul class="nav nav-tabs mb-4" id="residentInfoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">
                                <i class="fas fa-user mr-1"></i> Personal & ID
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="birth-tab" data-toggle="tab" href="#birth" role="tab" aria-controls="birth" aria-selected="false">
                                <i class="fas fa-calendar-alt mr-1"></i> Birth
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                                <i class="fas fa-address-card mr-1"></i> Contact
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content" id="residentInfoTabsContent">
                        <!-- Personal Information Tab (now including ID) -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <div class="row d-flex justify-content-center mb-2 position-relative">
                                <div class="profile-container">
                                    <!-- Profile image -->
                                    <img id="profilePreview" src="../admin/ProFile/<?=$row['PROFILE']?>" alt="" class="profile-img">
                                    
                                    <!-- Hidden file input -->
                                    <input type="file" name="profile" class="d-none" id="profileInput">
                                    
                                    <!-- Camera icon button -->
                                    <label for="profileInput" class="camera-button">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                </div>
                                
                                <!-- Keep existing profile path -->
                                <input type="hidden" name="existing_profile" value="<?=$row['PROFILE']?>">
                            </div>
                            <div class="row">
                                <!-- ID Information at the top -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Resident ID</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light"><i class="fas fa-id-badge"></i></span>
                                        </div>
                                        <input type="text" name="restid" class="form-control bg-light" value="<?= htmlspecialchars($row['RESIDENT_ID']) ?>" readonly>
                                    </div>
                                    <small class="form-text text-muted">System generated ID (cannot be modified)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Official ID</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="official_id" value="<?= htmlspecialchars($row['OFFICIAL_ID']) ?>">
                                    </div>
                                    <small class="form-text text-muted">Government issued ID number (optional)</small>
                                </div>
                                
                                <!-- Divider -->
                                <div class="col-12">
                                    <hr class="mt-0 mb-3">
                                </div>
                                
                                <!-- Personal Information -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($row['FIRST_NAME']) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Middle Name</label>
                                    <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($row['MIDDLE_NAME']) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($row['LAST_NAME']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Alias</label>
                                    <input type="text" class="form-control" name="alias" value="<?= htmlspecialchars($row['ALIAS']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select" name="gender" required>
                                        <option value="Male" <?= ($row['GENDER'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= ($row['GENDER'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Birth Information Tab -->
                        <div class="tab-pane fade" id="birth" role="tabpanel" aria-labelledby="birth-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="birth_date" value="<?= htmlspecialchars($row['BIRTH_DATE']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label font-weight-bold">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($row['AGE']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label font-weight-bold">Civil Status <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select" name="civil_status" required>
                                        <option value="Single" <?= ($row['CIVIL_STATUS'] == 'Single') ? 'selected' : '' ?>>Single</option>
                                        <option value="Married" <?= ($row['CIVIL_STATUS'] == 'Married') ? 'selected' : '' ?>>Married</option>
                                        <option value="Widowed" <?= ($row['CIVIL_STATUS'] == 'Widowed') ? 'selected' : '' ?>>Widowed</option>
                                        <option value="Divorced" <?= ($row['CIVIL_STATUS'] == 'Divorced') ? 'selected' : '' ?>>Divorced</option>
                                        <option value="Separated" <?= ($row['CIVIL_STATUS'] == 'Separated') ? 'selected' : '' ?>>Separated</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label font-weight-bold">Place of Birth <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="place_of_birth" value="<?= htmlspecialchars($row['PLACE_OF_BIRTH']) ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address & Contact Tab -->
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Purok <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="purok" value="<?= htmlspecialchars($row['PUROK']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Voter Status <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select" name="voter_status" required>
                                        <option value="Voter" <?= ($row['VOTER_STATUS'] == 'Voter') ? 'selected' : '' ?>>Voter</option>
                                        <option value="Non-Voter" <?= ($row['VOTER_STATUS'] == 'Non-Voter') ? 'selected' : '' ?>>Non-Voter</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($row['PHONE']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Email Address</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($row['EMAIL']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" name="update_resident" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- Edit Resident Modal end -->


    <!-- end Page Content -->

 </div>
 </div>


<!-- FOR CAMERA PREVIEW START FROM MODAL -->
 <script>
 document.getElementById('profileInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
<!-- FOR CAMERA PREVIEW END FROM MODAL -->
<?php include('includes/script.php') ?>
<?php ob_end_flush();  ?>
