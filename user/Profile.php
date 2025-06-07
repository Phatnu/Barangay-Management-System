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

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design


?>

<!-- Additional CSS for the profile design -->
<style>
.profile-header {
    height: 180px;
    border-radius: 8px 8px 0 0;
    position: relative;
    background-size: cover;
    background-position: center;
}
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #fff;
        position: absolute;
        left: 20px;
        bottom: -30px;
        background-color: #fff;
    }
    
    .profile-name {
        color: #fff;
        position: absolute;
        left: 120px;
        bottom: 50px;
        font-size: 24px;
        font-weight: 600;
    }
    
    .profile-username {
        color: rgba(255, 255, 255, 0.8);
        position: absolute;
        left: 120px;
        bottom: 30px;
        font-size: 14px;
    }
    
    .edit-profile-btn {
        position: absolute;
        right: 20px;
        bottom: 40px;
        background-color: #fff;
        color: #555;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .profile-nav {
        display: flex;
        padding: 0 15px;
        margin-top: 40px;
        border-bottom: 1px solid #eaeaea;
        overflow-x: auto;
    }
    
    .profile-nav-item {
        padding: 15px 20px;
        font-size: 14px;
        font-weight: 500;
        color: #555;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
    }
    
    .profile-nav-item.active {
        color: #8e44ad;
        border-bottom: 2px solid #8e44ad;
    }
    
    .profile-content {
        padding: 30px 20px;
    }
    
    .profile-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .profile-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .profile-info-item {
        display: flex;
        border-bottom: 1px solid #eee;
        padding-bottom: 12px;
    }
    
    .profile-info-label {
        width: 150px;
        font-weight: 600;
        color: #444;
        display: flex;
        align-items: center;
    }
    
    .profile-info-value {
        flex: 1;
        padding-left: 10px;
    }
    
    .card-header.bg-gradient-primary {
        background: #252762;
    }
    
    /* For responsive design */
    @media (max-width: 768px) {
        .profile-info-item {
            flex-direction: column;
            padding-bottom: 15px;
        }
        
        .profile-info-label {
            margin-bottom: 5px;
        }
        
        .profile-info-value {
            padding-left: 0;
        }
    }
</style>

<!-- Content Wrapper -->
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
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Profile Card with new design -->
        <div class="profile-card">
            <!-- Profile Header with Banner -->
<div class="profile-header" style="background-image: url('../admin/barangayimage/<?=$rowdesign['DASHBOARD_IMAGE']?>');">
    <img src="../admin/images/<?=$row['PROFILE']?>" class="profile-avatar" alt="Profile">
    <h1 class="profile-name"><?=$row['FIRST_NAME']?> <?=$row['LAST_NAME']?></h1>
    <div class="profile-username">@<?=$row['EMAIL']?></div>  
</div>
            <div class="profile-content">
    <div class="row mt-2">
        <div class="col-md-4 text-center mb-4">
        <div class="card shadow-sm">
        <div class="card-body" style="padding: 0px 0px 2rem 0px;!important">
               <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Personal Picture</h6>
                </div>
                <img src="" alt="">
            <img class="img-profile mb-3 mt-3" 
                 src="../admin/images/<?=$row['PROFILE']?>" 
                 style="width: 200px; height: 200px;">
            <h4><?=$row['FIRST_NAME']?> <?=$row['LAST_NAME']?></h4>
            <p class="text-muted"><?=$row['USERTYPE'] ?? 'Staff'?></p>

            <!-- Button trigger modal start -->
            <button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal" data-target="#editProfileModal">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
                <!-- Button trigger modal end -->
            </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-id-card mr-2 text-primary"></i>Staff ID
                            </div>
                            <div class="profile-info-value"><?=$row['STAFFID']?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-user mr-2 text-primary"></i>First Name
                            </div>
                            <div class="profile-info-value"><?=$row['FIRST_NAME']?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-user mr-2 text-primary"></i>Last Name
                            </div>
                            <div class="profile-info-value"><?=$row['LAST_NAME']?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-envelope mr-2 text-primary"></i>Email
                            </div>
                            <div class="profile-info-value"><?=$row['EMAIL'] ?? 'Not available'?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-birthday-cake mr-2 text-primary"></i>Birthday
                            </div>
                            <div class="profile-info-value"><?=$row['BIRTH_DAY'] ?? 'Not available'?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-venus-mars mr-2 text-primary"></i>Gender
                            </div>
                            <div class="profile-info-value"><?=$row['GENDER'] ?? 'Not available'?></div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-briefcase mr-2 text-primary"></i>Position
                            </div>
                            <div class="profile-info-value"><?=$row['USERTYPE'] ?? 'Not available'?></div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- End of Content Wrapper -->



<!-- Edit Profile Modal START -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #202b87;">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="../user/update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="staffid" value="<?= $row['STAFFID'] ?>">
                    <div class="row">
                        <!-- Profile Picture Section -->
                        <div class="col-md-4 text-center">
                            <div class="card p-3 shadow-sm">
                                <video id="cameraStream" autoplay class="img-thumbnail" style="display: none;"></video>
                                <img id="profilePreview" src="../admin/images/<?= $row['PROFILE'] ?>" class="img-thumbnail" style="width: auto; height: auto;">
                                <canvas id="captureCanvas" style="display: none;"></canvas>
                                <input type="file" name="profile" class="form-control mt-3" id="profileInput">
                                <div class="mt-3 d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCamera()">
                                        <i class="fas fa-camera"></i> Open Camera
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="captureImage()">
                                        <i class="fas fa-check-circle"></i> Capture
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- User Details Section -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?= $row['FIRST_NAME'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?= $row['LAST_NAME'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" name="email" value="<?= $row['EMAIL'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="birth_day">Birthday</label>
                                        <input type="date" class="form-control" name="birth_day" value="<?= $row['BIRTH_DAY'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" name="gender" required>
                                            <option value="Male" <?= ($row['GENDER'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= ($row['GENDER'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= ($row['GENDER'] == 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Position</label>
                                        <select class="form-control" name="position" required>
                                            <option value="Admin" <?= ($row['USERTYPE'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                                            <option value="Staff" <?= ($row['USERTYPE'] == 'Staff') ? 'selected' : '' ?>>Staff</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Profile Modal END -->





<!-- JS FOR THE CAMERA START -->
<script>
let videoStream = null;

// Open Camera
function openCamera() {
    let video = document.getElementById('cameraStream');
    let img = document.getElementById('profilePreview');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            videoStream = stream;
            video.srcObject = stream;
            video.style.display = 'block';
            img.style.display = 'none';
        })
        .catch(function(err) {
            console.error("Camera access denied: ", err);
        });
}

// Capture Image as JPG
function captureImage() {
    let video = document.getElementById('cameraStream');
    let canvas = document.getElementById('captureCanvas');
    let img = document.getElementById('profilePreview');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    let context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    let imageData = canvas.toDataURL('image/jpeg');
    img.src = imageData;

    // Stop Camera Stream
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }

    video.style.display = 'none';
    img.style.display = 'block';

    // Convert base64 image to file and set it in input field
    let fileInput = document.getElementById('profileInput');
    fetch(imageData)
        .then(res => res.blob())
        .then(blob => {
            let file = new File([blob], "captured_image.jpg", { type: "image/jpeg" });
            let dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        });
}

// File Input Preview
document.getElementById('profileInput').addEventListener('change', function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('profilePreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});
</script>
<!-- JS FOR THE CAMERA END -->

<?php 
include ('includes/script.php');
// include ('includes/footer.php');
ob_end_flush();  
?>