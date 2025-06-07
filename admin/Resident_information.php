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


// TO DISPLAY THE RESIDENT ON TABLE START
$selectaresident = "SELECT * FROM RESIDENT ORDER BY RESIDENT_ID DESC";
$resident = $con->query($selectaresident) or die($con->error);
// TO DISPLAY THE RESIDENT ON TABLE END

// do{
// echo $row['RESIDENT_ID']. " ".$row['OFFICIAL_ID']. " ".$row['FIRST_NAME']. " ".$row['MIDDLE_NAME']. " ".$row['LAST_NAME']. " ".$row['ALIAS']. " ".$row['PLACE_OF_BIRTH']. " ".$row['BIRTH_DATE']. " ".$row['CIVIL_STATUS']. " ".$row['GENDER']. " ".$row['PUROK']. " ".$row['VOTER_STATUS']. " ".$row['PHONE']. " ".$row['EMAIL']. " ".$row['PROFILE']. " <br>";
// }while($row = $resident->fetch_assoc())

// DELETE DATA FROM DATABASE START
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $deleteQuery = "DELETE FROM RESIDENT WHERE RESIDENT_ID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
          $_SESSION['toastr'] = "toastr.success('Successfully Deleted!');";
        header("location:Resident_information.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete resident!');</script>";
    }
}
// DELETE DATA FROM DATABASE END



if(isset($_POST['submitmodal'])){
    $file_name = $_FILES['profile']['name'];
    $tempname = $_FILES['profile']['tmp_name'];
    $folder = 'Profile/'. $file_name; // Unique filename to prevent overwriting

    // Validate file upload
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
    }

    if ($_FILES['profile']['size'] > 2 * 1024 * 1024) { // Limit to 2MB
        die("File size exceeds 2MB.");
    }

    // Move uploaded file
    if (!move_uploaded_file($tempname, $folder)) {
        die("File upload failed.");
    }


    $mofficialid = $_POST['mofficialid'];
    $mfirstname = $_POST['mfirstname'];
    $mmiddlename = $_POST['mmiddlename'];
    $mlastname = $_POST['mlastname'];
    $malias = $_POST['malias'];
    $mplace_of_birth = $_POST['mplace_of_birth'];
    $mbirth_date = $_POST['mbirth_date'];
    $mcivil_status = $_POST['mcivil_status'];
    $mgender = $_POST['mgender'];
    $mpurok = $_POST['mpurok'];
    $mvoter_status = $_POST['mvoter_status'];
    $mphone = $_POST['mphone'];
    $memail = $_POST['memail'];
    $mage = $_POST['mage'];


    $insertresident = "INSERT INTO `resident`(`OFFICIAL_ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `ALIAS`, `PLACE_OF_BIRTH`, `BIRTH_DATE`, `AGE`, `CIVIL_STATUS`, `GENDER`, `PUROK`, `VOTER_STATUS`, `PHONE`, `EMAIL`, `PROFILE`) 
    VALUES ('$mofficialid','$mfirstname','$mmiddlename','$mlastname','$malias','$mplace_of_birth','$mbirth_date','$mage','$mcivil_status','$mgender','$mpurok','$mvoter_status','$mphone','$memail', '$file_name')";

    $query_run = mysqli_query($con, $insertresident);

    if($query_run){
          $_SESSION['toastr'] = "toastr.success('Successfully Added new Resident!');";
        header("location:Resident_information.php");
        exit();
    }
    else{
        echo $con->error;
    }
}

// Query to count total female
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

<div style="color: black; padding: 10px;display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-webkit-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);-moz-box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.18);border-radius: 5px;">

<div style="border-left: 5px solid #0d6efd; padding-left: 10px;">
  <h5 style="margin: 0; font-weight: 600; color: #333;">
    Resident Information
    <span style="color: #0d6efd;">(<?php echo $total_resident; ?>)</span>
  </h5>
</div>


<button class="btn btn-primary" data-toggle="modal" data-target="#addResidentModal">Add New Resident</button>
</div>

<div class="table-background">
<table class="table table-bordered custom-table table-striped" id="myTable"> 
    <thead>
        <tr>
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
        <?php if ($resident->num_rows > 0): ?>
            <?php while ($row = $resident->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['FIRST_NAME'] ?></td>
                    <td><?= $row['MIDDLE_NAME'] ?></td>
                    <td><?= $row['LAST_NAME'] ?></td>
                    <td><?= $row['ALIAS'] ?></td>
                    <td><?= $row['BIRTH_DATE'] ?></td>
                    <td><?= $row['AGE'] ?></td>
                    <td><?= $row['CIVIL_STATUS'] ?></td>
                    <td><?= $row['GENDER'] ?></td>
                    <td><?= $row['PUROK'] ?></td>
                    <td><?= $row['VOTER_STATUS'] ?></td>
                    <td style="display: flex; justify-content: center; gap: .5rem">
                        <a href="Resident_information.php?delete=<?= $row['RESIDENT_ID'] ?>"><i style="color: #d1848c; font-size: .9rem" class="fas fa-trash"></i></a>
                        <a href="Resident_view.php?Resident=<?= $row['RESIDENT_ID'] ?>"><i style="font-size: .9rem;color:#46acc5" class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>

</div>

</div>
</div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->
 

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #202b87;">
                <h5 class="modal-title"><i class="fas fa-user"></i> New Resident Registration</h5>
             <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Side - Profile Image -->
                        <div class="col-md-4">
                            <div class="card text-center p-3">
                                <!-- Camera Preview -->
                                <video id="cameraStream" autoplay style="width: 100%; display: none;"></video>
                                <!-- Image Preview -->
                                <img src="images/placeholder.jpg" id="profilePreview" class="img-fluid rounded" style="width: 100%; height: auto;">
                                <canvas id="captureCanvas" style="display: none;"></canvas>
                                
                                <!-- File Input -->
                                <input type="file" name="profile" class="form-control mt-2" id="profileInput">

                                <!-- Camera Buttons -->
                                <div class="mt-2" style="display: flex;gap:1rem;justify-content:center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCamera()">
                                        <i class="fas fa-camera"></i> Camera
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="captureImage()">
                                        <i class="fas fa-check-circle"></i> Capture
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Resident Information -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name:</label>
                                        <input type="text" name="mfirstname" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Middle Name:</label>
                                        <input type="text" name="mmiddlename" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Last Name:</label>
                                        <input type="text" name="mlastname" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alias:</label>
                                        <input type="text" name="malias" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Place of Birth:</label>
                                        <input type="text" name="mplace_of_birth" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Birth Date:</label>
                                        <input type="date" name="mbirth_date" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Civil Status:</label>
                                        <select name="mcivil_status" class="form-control">
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Widowed</option>
                                            <option>Divorced</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Gender:</label>
                                        <select name="mgender" class="form-control">
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Purok:</label>
                                <input type="text" name="mpurok" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Voter Status:</label>
                                <select name="mvoter_status" class="form-control">
                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone No.:</label>
                                <input type="text" name="mphone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address:</label>
                                <input type="email" name="memail" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Age:</label>
                                <input type="text" name="mage" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">OFFICIAL_ID:</label>
                                <input type="text" name="mofficialid" class="form-control" value="<?=$rows['STAFFID']?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                        <button type="submit" class="btn btn-primary" name="submitmodal"><i class="fas fa-check"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



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


<?php include ('includes/script.php');?>
<?php include ('includes/footer.php');?>
<?php ob_end_flush();  ?>