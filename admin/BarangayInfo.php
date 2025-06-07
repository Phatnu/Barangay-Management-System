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
// TO DISPLAY THE INFO USING SESSION ID END

// FOR UPDATE CODE START
if (isset($_POST['Update'])) {
  $id = $_POST['informationID'];
  $province = $_POST['PROVINCE_NAME'];
  $town = $_POST['TOWN_NAME'];
  $barangay = $_POST['BARANGAY_NAME'];
  $contact = $_POST['CONTACT_NUMBER'];
  $dashboardText = $_POST['DASHBOARD_TEXT'];

  $municipalLogo = $_FILES['MUNICIPAL_LOGO']['name'];
  $barangayLogo = $_FILES['BARANGAY_LOGO']['name'];
  $dashboardImage = $_FILES['DASHBOARD_IMAGE']['name'];

  $targetDir = "barangayimage/";
  $updates = [];

  if (!empty($municipalLogo)) {
      move_uploaded_file($_FILES['MUNICIPAL_LOGO']['tmp_name'], $targetDir . $municipalLogo);
      $updates[] = "MUNICIPAL_LOGO = '$municipalLogo'";
  }

  if (!empty($barangayLogo)) {
      move_uploaded_file($_FILES['BARANGAY_LOGO']['tmp_name'], $targetDir . $barangayLogo);
      $updates[] = "BARANGAY_LOGO = '$barangayLogo'";
  }

  if (!empty($dashboardImage)) {
      move_uploaded_file($_FILES['DASHBOARD_IMAGE']['tmp_name'], $targetDir . $dashboardImage);
      $updates[] = "DASHBOARD_IMAGE = '$dashboardImage'";
  }

  $updates[] = "PROVINCE_NAME = '$province'";
  $updates[] = "TOWN_NAME = '$town'";
  $updates[] = "BARANGAY_NAME = '$barangay'";
  $updates[] = "CONTACT_NUMBER = '$contact'";
  $updates[] = "DASHBOARD_TEXT = '$dashboardText'";

  $updateSQL = "UPDATE Barangay_Info SET " . implode(", ", $updates) . " WHERE ID = '$id'";
  $con->query($updateSQL);

  echo "<script>
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Barangay Information Updated Successfully!',
      confirmButtonColor: '#4e73df'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'BarangayInfo.php';
      }
    });
  </script>";
}
// FOR UPDATE CODE END

// FOR INSERT CODE START
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['Update'])) {
  $province = $_POST['PROVINCE_NAME'];
  $town = $_POST['TOWN_NAME'];
  $barangay = $_POST['BARANGAY_NAME'];
  $contact = $_POST['CONTACT_NUMBER'];
  $dashboard_text = $_POST['DASHBOARD_TEXT'];

  $targetDir = "barangayimage/";

  if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
  }

  $municipal_logo_name = uniqid("municipal_") . "_" . basename($_FILES["MUNICIPAL_LOGO"]["name"]);
  $barangay_logo_name = uniqid("barangay_") . "_" . basename($_FILES["BARANGAY_LOGO"]["name"]);
  $dashboard_image_name = uniqid("dashboard_") . "_" . basename($_FILES["DASHBOARD_IMAGE"]["name"]);

  $municipal_logo = $targetDir . $municipal_logo_name;
  $barangay_logo = $targetDir . $barangay_logo_name;
  $dashboard_image = $targetDir . $dashboard_image_name;

  move_uploaded_file($_FILES["MUNICIPAL_LOGO"]["tmp_name"], $municipal_logo);
  move_uploaded_file($_FILES["BARANGAY_LOGO"]["tmp_name"], $barangay_logo);
  move_uploaded_file($_FILES["DASHBOARD_IMAGE"]["tmp_name"], $dashboard_image);

  $sql = "INSERT INTO `Barangay_Info` 
  (`PROVINCE_NAME`, `TOWN_NAME`, `BARANGAY_NAME`, `CONTACT_NUMBER`, `DASHBOARD_TEXT`, `MUNICIPAL_LOGO`, `BARANGAY_LOGO`, `DASHBOARD_IMAGE`)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $con->prepare($sql);
  $stmt->bind_param("ssssssss", 
      $province, $town, $barangay, $contact, $dashboard_text, 
      $municipal_logo_name, $barangay_logo_name, $dashboard_image_name
  );

  if ($stmt->execute()) {
      echo "<script>
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Barangay information saved successfully!',
          confirmButtonColor: '#4e73df'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'BarangayInfo.php';
          }
        });
      </script>";
  } else {
      echo "<script>
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Error inserting data: " . $stmt->error . "',
          confirmButtonColor: '#e74a3b'
        });
      </script>";
  }

  $stmt->close();
}
// FOR INSERT CODE END

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
?>

<style>
.main-container {
 
    min-height: 100vh;
    padding: 2rem 1rem;
}

.info-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 2rem;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.card-body {
    padding: 2.5rem;
    background: #f8f9fc;
}

.form-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #4e73df;
}

.section-title {
    color: #4e73df;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control {
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: #f8f9fc;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    background: white;
}

.form-control:disabled {
    background: #f8f9fc;
    border-color: #e3e6f0;
    color: #5a5c69;
}

.form-label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.image-upload-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #1cc88a;
}

.image-preview {
    position: relative;
    border: 3px dashed #e3e6f0;
    border-radius: 15px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8f9fc;
}

.image-preview:hover {
    border-color: #4e73df;
    background: rgba(78, 115, 223, 0.05);
}

.preview-image {
    max-height: 180px;
    width: auto;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.preview-image:hover {
    transform: scale(1.05);
}

.upload-placeholder {
    color: #858796;
    font-size: 0.9rem;
    padding: 2rem;
}

.btn-custom {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
}

.btn-custom:hover {
    background: linear-gradient(135deg, #224abe 0%, #1a365d 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: linear-gradient(135deg, #17a673 0%, #138b5f 100%);
    transform: translateY(-2px);
    color: white;
}

.btn-cancel {
    background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    margin-left: 0.5rem;
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    transform: translateY(-2px);
    color: white;
}

.action-buttons {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.topbar {
    background: white !important;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    border-radius: 0 0 20px 20px;
    margin-bottom: 2rem;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    color: white;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .card-header, .card-body, .form-section, .action-buttons {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
}
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow mx-1 mt-4" style="display: flex;gap:5px">
                <i class="fas fa-clock text-secondary me-2"></i>
                <p id="currentTime" style="color: gray;font-size:12px"></p>
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$rows['FIRST_NAME']?></label>
                    </span>
                    <img class="img-profile rounded-circle" src="images/<?=$rows['PROFILE']?>">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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

    <div class="main-container">
        <div class="container-fluid">
            <form method="post" action="BarangayInfo.php" enctype="multipart/form-data">
                <div class="info-card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-2">
                                    <i class="fas fa-building mr-3"></i>
                                    Barangay Information
                                </h2>
                                <p class="mb-0 opacity-75">Manage your barangay's essential information and settings</p>
                            </div>
                            <div>
                                <span class="status-badge">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <input type="hidden" name="informationID" value="<?=$rowdesign['ID'] ?? ''?>">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Basic Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="province" class="form-label">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Province Name
                                    </label>
                                    <input type="text" disabled="true" class="form-control" id="province" 
                                           name="PROVINCE_NAME" value="<?=$rowdesign['PROVINCE_NAME'] ?? ''?>" 
                                           placeholder="Enter province name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="town" class="form-label">
                                        <i class="fas fa-city mr-2"></i>Town/Municipality Name
                                    </label>
                                    <input type="text" disabled="true" class="form-control" id="town" 
                                           name="TOWN_NAME" value="<?=$rowdesign['TOWN_NAME'] ?? ''?>" 
                                           placeholder="Enter town/municipality name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="barangay" class="form-label">
                                        <i class="fas fa-home mr-2"></i>Barangay Name
                                    </label>
                                    <input type="text" disabled="true" class="form-control" id="barangay" 
                                           name="BARANGAY_NAME" value="<?=$rowdesign['BARANGAY_NAME'] ?? ''?>" 
                                           placeholder="Enter barangay name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact" class="form-label">
                                        <i class="fas fa-phone mr-2"></i>Contact Number
                                    </label>
                                    <input type="tel" disabled="true" class="form-control" id="contact" 
                                           name="CONTACT_NUMBER" pattern="[0-9]{11}" 
                                           value="<?=$rowdesign['CONTACT_NUMBER'] ?? ''?>" 
                                           placeholder="e.g. 09123456789">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="dashboardText" class="form-label">
                                    <i class="fas fa-edit mr-2"></i>Dashboard Welcome Text
                                </label>
                                <textarea class="form-control" disabled="true" id="dashboardText" 
                                          name="DASHBOARD_TEXT" rows="4" 
                                          placeholder="Enter welcome message for dashboard"><?=$rowdesign['DASHBOARD_TEXT'] ?? ''?></textarea>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="image-upload-section">
                            <h5 class="section-title">
                                <i class="fas fa-images" style="color: #1cc88a;"></i>
                                Logo & Images
                            </h5>

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label for="municipalLogo" class="form-label">
                                        <i class="fas fa-flag mr-2"></i>Municipal Logo
                                    </label>
                                    <div class="image-preview">
                                        <input disabled="true" type="file" class="form-control mb-3" 
                                               id="municipalLogo" name="MUNICIPAL_LOGO" 
                                               accept=".png, .jpg, .jpeg" 
                                               onchange="previewImage(this, 'municipalPreview')">
                                        
                                        <?php if (!empty($rowdesign['MUNICIPAL_LOGO'])): ?>
                                            <img id="municipalPreview" class="preview-image" 
                                                 src="barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>" 
                                                 alt="Municipal Logo" />
                                        <?php else: ?>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                                <p>Upload Municipal Logo</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="barangayLogo" class="form-label">
                                        <i class="fas fa-shield-alt mr-2"></i>Barangay Logo
                                    </label>
                                    <div class="image-preview">
                                        <input disabled="true" type="file" class="form-control mb-3" 
                                               id="barangayLogo" name="BARANGAY_LOGO" 
                                               accept=".png, .jpg, .jpeg" 
                                               onchange="previewImage(this, 'barangayPreview')">
                                        
                                        <?php if (!empty($rowdesign['BARANGAY_LOGO'])): ?>
                                            <img id="barangayPreview" class="preview-image" 
                                                 src="barangayimage/<?=$rowdesign['BARANGAY_LOGO']?>" 
                                                 alt="Barangay Logo" />
                                        <?php else: ?>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                                <p>Upload Barangay Logo</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="dashboardImage" class="form-label">
                                        <i class="fas fa-image mr-2"></i>Dashboard Image
                                    </label>
                                    <div class="image-preview">
                                        <input disabled="true" type="file" class="form-control mb-3" 
                                               id="dashboardImage" name="DASHBOARD_IMAGE" 
                                               accept=".png, .jpg, .jpeg" 
                                               onchange="previewImage(this, 'dashboardPreview')">
                                        
                                        <?php if (!empty($rowdesign['DASHBOARD_IMAGE'])): ?>
                                            <img id="dashboardPreview" class="preview-image" 
                                                 src="barangayimage/<?=$rowdesign['DASHBOARD_IMAGE']?>" 
                                                 alt="Dashboard Image" />
                                        <?php else: ?>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                                <p>Upload Dashboard Image</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="button" class="btn btn-edit btn-lg" name="edit" id="editBtn">
                                <i class="fas fa-edit mr-2"></i>Edit Information
                            </button>
                            <button type="submit" class="btn btn-custom btn-lg" name="Update" disabled="true" id="updateBtn">
                                <i class="fas fa-save mr-2"></i>Update Information
                            </button>
                            <button type="button" class="btn btn-cancel btn-lg" id="cancelBtn" style="display: none;">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Enhanced JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById('editBtn');
    const updateButton = document.getElementById('updateBtn');
    const cancelButton = document.getElementById('cancelBtn');
    const inputs = document.querySelectorAll('form input, form textarea');
    const originalValues = {};

    // Store original values
    inputs.forEach(input => {
        originalValues[input.name] = input.value;
    });

    editButton.addEventListener("click", function (e) {
        e.preventDefault();
        
        // Enable all inputs
        inputs.forEach(input => {
            input.removeAttribute("disabled");
            input.style.background = 'white';
        });

        // Toggle buttons
        updateButton.removeAttribute("disabled");
        editButton.style.display = "none";
        cancelButton.style.display = "inline-block";
        
        // Add animation
        document.querySelector('.info-card').style.transform = 'scale(1.02)';
        setTimeout(() => {
            document.querySelector('.info-card').style.transform = 'scale(1)';
        }, 200);
    });

    cancelButton.addEventListener("click", function (e) {
        e.preventDefault();
        
        // Restore original values
        inputs.forEach(input => {
            input.value = originalValues[input.name] || '';
            input.setAttribute("disabled", "true");
            input.style.background = '#f8f9fc';
        });

        // Toggle buttons
        updateButton.setAttribute("disabled", "true");
        editButton.style.display = "inline-block";
        cancelButton.style.display = "none";
    });

    // Enhanced image preview function
    window.previewImage = function(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                } else {
                    // Create new preview image
                    const img = document.createElement('img');
                    img.id = previewId;
                    img.className = 'preview-image';
                    img.src = e.target.result;
                    input.parentNode.querySelector('.upload-placeholder')?.remove();
                    input.parentNode.appendChild(img);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Real-time clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString();
        document.getElementById('currentTime').textContent = `${dateString} ${timeString}`;
    }
    
    updateClock();
    setInterval(updateClock, 1000);
});
</script>

<!-- Include SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include ('includes/script.php');?>
<?php include ('includes/footer.php');?>
<?php ob_end_flush(); ?>