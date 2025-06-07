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

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design

// TO DISPLAY THE INFO USING SESSION ID START
$user_id = $_SESSION['STAFFID'];
$sql = "SELECT * FROM STAFF WHERE STAFFID = '$user_id'";
$query = $con->query($sql);
$rows = $query->fetch_assoc();
// TO DISPLAY THE INFO USING SESSION ID START

// $query2 = $con->query($sql2);
$sql2 = "SELECT * FROM OFFICIAL";
$query2 = $con->query($sql2);

// ADD OFFICIAL START
if (isset($_POST['add_official'])) {
    $full_name    = $_POST['full_name'];
    $chairmanship = $_POST['chairmanship'];
    $position     = $_POST['position'];
    $term_start   = $_POST['term_start'];
    $term_end     = $_POST['term_end'];
    $status       = $_POST['status'];

    $file = $_FILES['profile'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($file['type'], $allowed_types)) {
        echo "<script>alert('Invalid file type. Only JPG or PNG allowed.'); window.history.back();</script>";
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        echo "<script>alert('File is too large. Max 5MB allowed.'); window.history.back();</script>";
        exit;
    }

    // Normalize position input (e.g., make 'captain' → 'Captain')
    $normalized_position = ucfirst(strtolower($position));

    // Prevent duplicate 'Captain' position (case-insensitive)
    if (strtolower($normalized_position) == 'captain') {
        $check = mysqli_query($con, "SELECT * FROM OFFICIAL WHERE LOWER(POSITION) = 'captain'");

        if (mysqli_num_rows($check) > 0) {
            $_SESSION['toastr'] = "toastr.warning('A Captain already exists. Cannot add another.');";
            header("location:Officials.php");
            // echo "<script>alert('A Captain already exists. Cannot add another.'); window.history.back();</script>";
            exit;
        }
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_file_name = uniqid() . "." . $ext;
    $folder = 'officialpic/' . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $folder)) {
        $insert = "INSERT INTO OFFICIAL 
            (FULL_NAME, CHAIRMANSHIP, POSITION, TERM_START, TERM_END, STATUS, PROFILE) 
            VALUES 
            ('$full_name', '$chairmanship', '$normalized_position', '$term_start', '$term_end', '$status', '$new_file_name')";

        if (mysqli_query($con, $insert)) {
                $_SESSION['toastr'] = "toastr.success('Successfully Added new Officials!');";
            header("Location: Officials.php?success=1");
            exit;
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
    }
}

// ADD OFFICIAL END

// DELETE DATA FROM DATABASE START
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $deleteQuery = "DELETE FROM OFFICIAL WHERE ID = '$id'";
    $result = $con->query($deleteQuery);

    if ($result) {
      $_SESSION['toastr'] = "toastr.success('Successfully deleted!');";
        header("location:Officials.php");
        exit();
    } else {
        echo "<script>alert('Failed to delete resident!');</script>";
    }
}
// DELETE DATA FROM DATABASE END

// EDIT MODAL START
$editOfficialData = null;

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $getQuery = "SELECT * FROM OFFICIAL WHERE ID = '$edit_id'";
    $getResult = $con->query($getQuery);

    if ($getResult->num_rows > 0) {
        $editOfficialData = $getResult->fetch_assoc();
    }
}
// EDIT MODAL END


// UPDATE MODAL START
if (isset($_POST['update_official'])) {
    $id           = $_POST['edit_id'];
    $full_name    = $_POST['full_name'];
    $chairmanship = $_POST['chairmanship'];
    $position     = $_POST['position'];
    $term_start   = $_POST['term_start'];
    $term_end     = $_POST['term_end'];
    $status       = $_POST['status'];

    // Normalize position (e.g., "captain", "CAPTAIN") to "Captain"
    $normalized_position = ucfirst(strtolower($position));

    // Prevent multiple Captains (except for the same ID)
    if (strtolower($normalized_position) === 'captain') {
        $check = mysqli_query($con, "SELECT ID FROM OFFICIAL WHERE LOWER(POSITION) = 'captain' AND ID != '$id'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('A Captain already exists. Cannot assign another.'); window.history.back();</script>";
            exit;
        }
    }

    // Get existing profile image
    $result = $con->query("SELECT PROFILE FROM OFFICIAL WHERE ID = '$id'");
    $row = $result->fetch_assoc();
    $existing_profile = $row['PROFILE'];

    // Handle file upload
    $file = $_FILES['profileedit'];
    $file_name = $file['name'];
    $new_file_name = $existing_profile;

    if (!empty($file_name)) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed_types)) {
            echo "<script>alert('Invalid file type. Only JPG or PNG allowed.'); window.history.back();</script>";
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('File is too large. Max 5MB allowed.'); window.history.back();</script>";
            exit;
        }

        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . "." . $ext;
        $folder = 'officialpic/' . $new_file_name;

        if (!move_uploaded_file($file['tmp_name'], $folder)) {
            echo "<script>alert('Failed to upload new profile image.'); window.history.back();</script>";
            exit;
        }
    }

    // Perform the update
    $update = "UPDATE OFFICIAL SET 
        FULL_NAME = '$full_name', 
        CHAIRMANSHIP = '$chairmanship', 
        POSITION = '$normalized_position', 
        TERM_START = '$term_start', 
        TERM_END = '$term_end', 
        PROFILE = '$new_file_name', 
        STATUS = '$status' 
        WHERE ID = $id";

    if (mysqli_query($con, $update)) {
            $_SESSION['toastr'] = "toastr.success('Successfully Updated!');";
        header("Location: Officials.php?success=1");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
// UPDATE MODAL END

?>
<style>
.center-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.profile-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
}

.profile-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  background-color: #f0f0f0;
}

.camera-icon {
  position: absolute;
  bottom: 0;
  right: 0;
  background-color: #fff;
  border-radius: 50%;
  padding: 6px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.camera-icon i {
  font-size: 14px;
  color: #555;
}
  .officials-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    padding: 24px;
    max-width: 1400px;
}

.official-card {
    background: #ffffff;
    border-radius: 4px!important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
}

.official-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.profile-image {
    height: 200px; /* or smaller if you like */
    background-size: contain; /* Show full image */
    background-position: center;
    background-repeat: no-repeat;
    background-color: #f8fafc;
    margin-top: 2rem;
    border-radius: 5px;
}


.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.3));
}

.card-content {
    padding: 20px;
    text-align: center;
}

.official-info {
    margin-bottom: 16px;
}

.official-name {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 8px 0;
    line-height: 1.3;
}

.chairmanship {
    font-size: 16px;
    font-weight: 600;
    color: #3b82f6;
    margin-bottom: 4px;
    min-height: 20px;
}

.position {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.action-buttons {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.official-card:hover .action-buttons {
    opacity: 1;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.2s ease;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.edit-btn {
    background: rgba(59, 130, 246, 0.9);
    color: white;
}

.edit-btn:hover {
    background: #3b82f6;
    transform: scale(1.1);
}

.delete-btn {
    background: rgba(239, 68, 68, 0.9);
    color: white;
}

.delete-btn:hover {
    background: #ef4444;
    transform: scale(1.1);
}

.action-btn i {
    font-size: 14px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .officials-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 20px;
    }
}

@media (max-width: 768px) {
    .officials-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        padding: 16px;
    }
    
    .profile-image {
        height: 240px;
    }
    
    .official-name {
        font-size: 16px;
    }
    
    .chairmanship {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .officials-container {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 12px;
    }
    
    .profile-image {
        height: 280px;
    }
}

/* Print Styles */
@media print {
    .officials-container {
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        padding: 15px;
    }
    
    .official-card {
        box-shadow: none;
        border: 1px solid #e5e7eb;
        break-inside: avoid;
    }
    
    .action-buttons {
        display: none;
    }
    
    .official-card:hover {
        transform: none;
    }
}
        .page-header {
            text-align: center;
            padding: 0 2rem;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .page-header .subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
             text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        

.officials-section {
    background: linear-gradient(rgba(33, 47, 112, 0.9), rgba(33, 47, 112, 0.9)),
                url('../images/background.jpg');
    background-size: cover;
    background-position: center;
    padding: 4rem 2rem;
    min-height: calc(100vh - 70px);
}

.officials-header {
    text-align: center;
    margin-bottom: 3rem;
    color: white;
}

.officials-header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.officials-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.officials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.official-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.official-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.official-image {
    position: relative;
    padding-top: 100%;
    background-size: cover;
    background-position: center;
}

.official-content {
    padding: 1.5rem;
    text-align: center;
    position: relative;
}

.official-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212f70;
    margin-bottom: 0.5rem;
}

.official-role {
    color: #4a5568;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.official-position {
    color: #2d3748;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-buttons {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.official-card:hover .action-buttons {
    opacity: 1;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.2s ease;
}

.edit-btn {
    background: rgba(59, 130, 246, 0.9);
}

.delete-btn {
    background: rgba(239, 68, 68, 0.9);
}

.action-btn:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .officials-header h1 {
        font-size: 2rem;
    }
    
    .officials-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}
</style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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



<!-- OFFICIAL START -->
 <div style="background-image: url('../images/cards.jpg'); background-size: cover; background-position: center; padding: 1rem 2rem; min-height: calc(100vh - 70px);">
<div style="padding:20px;">
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-end mb-0">
 
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addOfficialModal">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Add Official
                        </a>

                    </div>


                     </div>


</div>
<div class="page-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
  <!-- Left Logo -->
  <div class="logo-left">
    <img src="barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>" alt="Left Logo" style="height: 80px;">
  </div>

  <!-- Center Content -->
  <div style="text-align: center; flex: 1;">
    <h1 style="color: #212f70; margin-bottom: 5px;">BARANGAY OFFICIALS</h1>
    <p style="color: #212f70;" class="subtitle">Meet Our Dedicated Community Leaders</p>
    <p style="color: #444; max-width: 800px; margin: 10px auto;">
      Our barangay officials are dedicated to maintaining peace, delivering essential services, and supporting community growth. Get to know the leaders working to build a better barangay for all.
    </p>
    <hr>
  </div>

  <!-- Right Logo -->
  <div class="logo-right">
    <img src="barangayimage/<?=$rowdesign['BARANGAY_LOGO']?>" alt="Right Logo" style="height: 80px;">
  </div>
</div>

<div style="display: flex;justify-content:center">

<div class="officials-container">
   
    <?php 
    // Rewind result pointer if needed
    $query2->data_seek(0);

    while ($rowof = $query2->fetch_assoc()) { 
        $status = $rowof['STATUS'];
        $statusColor = $status == 'Active' ? '#10b981' : ($status == 'Inactive' ? '#ef4444' : '#6b7280');
        $statusBg = $status == 'Active' ? '#d1fae5' : ($status == 'Inactive' ? '#fee2e2' : '#f3f4f6');
        $statusBorder = $status == 'Active' ? '#10b981' : ($status == 'Inactive' ? '#ef4444' : '#d1d5db');
    ?>
    <div class="official-card" data-aos="fade-up"
     data-aos-duration="3000">
        <!-- Profile Image -->
        <div class="profile-image" style="background-image: url('officialpic/<?php echo htmlspecialchars($rowof['PROFILE']); ?>');">
            <div class="image-overlay"></div>
        </div>
        
        <!-- Card Content -->
        <div class="card-content">
            <div class="official-info">
                <h3 class="official-name"><?php echo htmlspecialchars($rowof['FULL_NAME']); ?></h3>
                <div class="chairmanship"><?php echo htmlspecialchars($rowof['CHAIRMANSHIP']); ?></div>
                <div class="position"><?php echo htmlspecialchars($rowof['POSITION']); ?></div>
            </div>
            
            <div class="status-badge" style="background: <?php echo $statusBg; ?>; color: <?php echo $statusColor; ?>; border: 1px solid <?php echo $statusBorder; ?>;">
                <span class="status-dot" style="background: <?php echo $statusColor; ?>;"></span>
                <?php echo htmlspecialchars($status); ?>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="Officials.php?edit=<?php echo $rowof['ID']; ?>" class="action-btn edit-btn" title="Edit Official">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <a href="Officials.php?delete=<?php echo $rowof['ID']; ?>" class="action-btn delete-btn" title="Delete Official" onclick="return confirm('Are you sure you want to delete this official?')">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    </div>
    <?php } ?>
</div>
 
</div>
<!-- OFFICIAL END -->       
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

   

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->


<!-- Add Official Modal -->
<div class="modal fade" id="addOfficialModal" tabindex="-1" role="dialog" aria-labelledby="addOfficialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <form action="Officials.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addOfficialModalLabel">Add New Official</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <!-- Profile Image Centered -->
          <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
              <img src="images/placeholder.jpg" id="profilePreview" alt="Profile Image" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
              <label for="profileInput" class="position-absolute" style="bottom: 0; right: 0; background: white; border-radius: 50%; padding: 6px; cursor: pointer;">
                <i class="fas fa-camera"></i>
              </label>
              <input type="file" id="profileInput" name="profile" accept="image/*" style="display: none;">
            </div>
          </div>

          <!-- Two Column Form Layout -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Full Name</label>
              <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Chairmanship</label>
              <select class="form-control" name="chairmanship" required>
                <option value="">-- Select Chairmanship --</option>
                <?php
                include_once("../connections/connection.php");
                $con = connection();
                $res = $con->query("SELECT TITLE FROM CHAIRMANSHIP");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['TITLE']) . "'>" . htmlspecialchars($row['TITLE']) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Position</label>
              <select class="form-control" name="position" required>
                <option value="">-- Select Position --</option>
                <?php
                $res = $con->query("SELECT POSITION FROM BARANGAY_POSITION");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['POSITION']) . "'>" . htmlspecialchars($row['POSITION']) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Status</label>
              <select name="status" class="form-control" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Term Start</label>
              <input type="date" name="term_start" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Term End</label>
              <input type="date" name="term_end" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="add_official" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- ADD MODAL END -->


<!-- Edit Official Modal START -->
<?php if ($editOfficialData): ?>
<div class="modal fade show d-block" id="editOfficialModal" tabindex="-1" role="dialog" style="background:rgba(0,0,0,0.5); overflow-y:auto;">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <form action="Officials.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="edit_id" value="<?= $editOfficialData['ID'] ?>">

      <div class="modal-content rounded-4 shadow">
        <div class="modal-header text-white bg-primary">
          <h5 class="modal-title">Edit Official</h5>
          <a href="Officials.php" class="btn-close btn-close-white" aria-label="Close"></a>
        </div>

        <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
          
          <!-- Profile Image Centered -->
          <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
              <img src="officialpic/<?= $editOfficialData['PROFILE'] ?>" id="profilePreview2" alt="Profile Image" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
              <label for="profileInput2" class="position-absolute" style="bottom: 0; right: 0; background: white; border-radius: 50%; padding: 6px; cursor: pointer;">
                <i class="fas fa-camera"></i>
              </label>
              <input type="file" id="profileInput2" name="profileedit" accept="image/*" style="display: none;">
            </div>
          </div>

          <!-- Two Column Form -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?= $editOfficialData['FULL_NAME'] ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Chairmanship</label>
                <select class="form-control" name="chairmanship" required>
                    <option value="">-- Select Chairmanship --</option>
                    <?php
                    include_once("../connections/connection.php");
                    $con = connection();
                    $res = $con->query("SELECT TITLE FROM CHAIRMANSHIP");
                    while ($row = $res->fetch_assoc()) {
                        $selected = ($editOfficialData['CHAIRMANSHIP'] === $row['TITLE']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['TITLE']) . "' $selected>" . htmlspecialchars($row['TITLE']) . "</option>";
                    }
                    ?>
                </select>
                </div>

                <div class="col-md-6 mb-3">
            <label>Position</label>
            <select class="form-control" name="position" required>
                <option value="">-- Select Position --</option>
                <?php
                $res = $con->query("SELECT POSITION FROM BARANGAY_POSITION");
                while ($row = $res->fetch_assoc()) {
                    $selected = ($editOfficialData['POSITION'] === $row['POSITION']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($row['POSITION']) . "' $selected>" . htmlspecialchars($row['POSITION']) . "</option>";
                }
                ?>
            </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Status</label>
              <select name="status" class="form-control" required>
                <option value="Active" <?= $editOfficialData['STATUS'] == 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $editOfficialData['STATUS'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Term Start</label>
              <input type="date" name="term_start" class="form-control" value="<?= !empty($editOfficialData['TERM_START']) ? date('Y-m-d', strtotime($editOfficialData['TERM_START'])) : '' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label>Term End</label>
              <input type="date" name="term_end" class="form-control" value="<?= !empty($editOfficialData['TERM_END']) ? date('Y-m-d', strtotime($editOfficialData['TERM_END'])) : '' ?>">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="Officials.php" class="btn btn-danger">Cancel</a>
          <button type="submit" name="update_official" class="btn btn-primary">Update Official</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

</div>



</div>

</div>

<!-- ✅ Image Preview Script -->
<script>
  document.getElementById('profileInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profilePreview');

    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      preview.src = "images/placeholder.jpg";
    }
  });
</script>

<?php include ('includes/script.php');?>
<?php include ('includes/footer.php');?>

<?php ob_end_flush();  ?>

