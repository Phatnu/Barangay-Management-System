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
// TO DISPLAY THE INFO USING SESSION ID START


// DISPLAY TO OFFICIAL TABLE START
// $sql2 = "SELECT * FROM STAFF WHERE USERTYPE = 'Admin'";
// $query2 = $con->query($sql2);
$sql2 = "SELECT * FROM OFFICIAL";
$query2 = $con->query($sql2);

// DISPLAY TO OFFICIAL TABLE END

// Query to count total residents
$query = "SELECT COUNT(*) AS total FROM resident";
$result = mysqli_query($con, $query);
$rows = mysqli_fetch_assoc($result); // Not $rows
$total_residents = $rows['total'];

// Query to count total male
$query = "SELECT COUNT(*) AS total FROM resident WHERE GENDER = 'MALE'";
$result = mysqli_query($con, $query);
$rows = mysqli_fetch_assoc($result); // Not $rows
$total_male = $rows['total'];

// Query to count total female
$query = "SELECT COUNT(*) AS total FROM resident WHERE GENDER = 'FEMALE'";
$result = mysqli_query($con, $query);
$rows = mysqli_fetch_assoc($result); // Not $rows
$total_female = $rows['total'];

// Query to count total female
$query = "SELECT COUNT(*) AS total FROM resident WHERE VOTER_STATUS = 'YES'";
$result = mysqli_query($con, $query);
$rows = mysqli_fetch_assoc($result); // Not $rows
$total_voters = $rows['total'];


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
            echo "<script>alert('A Captain already exists. Cannot add another.'); window.history.back();</script>";
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
            header("Location: dashboard.php?success=1");
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
        $_SESSION['toastr'] = "toastr.success('Successfully Deleted Official');";
        header("location:Dashboard.php");
        exit();
    } else {
        $_SESSION['toastr'] = "toastr.error('Failed to delete Official!');";

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
        header("Location: Dashboard.php?success=1");
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
.stat-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 25px;
}

.chart-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
}
</style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                       
                        <li class="nav-item dropdown no-arrow mx-1 mt-4" style="display: flex;gap:5px">
                        <i class="fas fa-clock text-secondary me-2"></i><p id="currentTime" style="color: gray;font-size:12px"></p>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$row['FIRST_NAME']?></label>                                </span>
                                <img class="img-profile rounded-circle"
                                src="images/<?=$row['PROFILE']?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../admin/Profile.php">
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-0">
                        <div style="display: block;">
                        <h1 class="h3 mb-0 text-gray-800" style="font-weight: bold;color:black">Dashboard Overview</h1>
                        <p style="color: gray;">Welcome back, Admin!</p>
                        </div>
                        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addOfficialModal">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Add Official
                        </a> -->
                        <p>
                        Home / <span style="color: #283593;">Dashboard</span>
                        </p>
                    </div>

                <div class="row">
    <!-- Total Residents -->
    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-left" data-aos-delay="300">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Residents</div>
                        <div class="h5 font-weight-bold text-gray-800"><?php echo $total_residents; ?></div>
                    </div>
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Male -->
    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-left" data-aos-delay="600">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Male</div>
                        <div class="h5 font-weight-bold text-gray-800"><?php echo $total_male; ?></div>
                    </div>
                    <i class="fas fa-mars fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Female -->
    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-left" data-aos-delay="900">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Female</div>
                        <div class="h5 font-weight-bold text-gray-800"><?php echo $total_female; ?></div>
                    </div>
                    <i class="fas fa-venus fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Voters -->
    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-left" data-aos-delay="1200">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Voters</div>
                        <div class="h5 font-weight-bold text-gray-800"><?php echo $total_voters; ?></div>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
                    </div>

                     </div>

                <div class="row" style="padding: 0px 20px;">
                <!-- Line Graph: Wider column -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                    <div class="card-body">
                        <canvas id="myChart" height="120"></canvas>
                    </div>
                    </div>
                </div>

                <!-- Pie Chart: Smaller column -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow">
                    <div class="card-body">
                        <p style="text-align:center;font-size:13px">Gender Report Per Month</p>
                        <canvas id="genderChart" width="300" height="230"></canvas>
                    </div>
                    </div>
                </div>
                </div>



                  <div class="row" style="padding: 0px 20px;">
                <div class="col-lg-12 mb-4">
                    <div class="card shadow">
                    <div class="card-body">
                        <canvas id="blotterChart" width="300" height="80"></canvas>
                    </div>
                    </div>
                </div>
                   </div>

</div>

<div class="row" style="padding: 0px 20px;display:none">
    <div class="col-lg-12 col-md-12 mb-4">
        <!-- Your table here -->
        <div class="card shadow mb-4">
            <div class="card-header text-white">
                <h6 class="m-0 font-weight-bold" style="color: gray;">Current Barangay Officials</h6>
            </div>
            <div class="card-body">
                <!-- Your actual table content goes here -->
              <!-- Your actual table content goes here -->
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="text-align:center">Fullname</th>
            <th style="text-align:center">Chairmanship</th>
            <th style="text-align:center">Position</th>
            <th style="text-align:center">Profile</th>
            <th style="text-align:center">Status</th>
            <th style="text-align:center">Action</th>
        </tr>
    </thead>
    <tbody>
        <!-- Loop through all rows from the database -->
        <?php while ($rowof = $query2->fetch_assoc()) { ?>
        <tr>
            <td style="text-align:center;font-size:14px"><?php echo $rowof['FULL_NAME']; ?></td>
            <td style="text-align:center;font-size:14px"><?php echo $rowof['CHAIRMANSHIP']; ?></td> <!-- You can customize this if needed -->
            <td style="text-align:center;font-size:14px"><?php echo $rowof['POSITION']?></td>   
            <td style="text-align:center;font-size:14px"><img src="officialpic/<?php echo $rowof['PROFILE']; ?>" alt="Profile" width="40" height="40" style="object-fit: cover; border-radius: 50%;border:1px solid #35366c"></td>
            <td style="text-align:center;font-size:14px">
            <?php
            $status = $rowof['STATUS'];

            if ($status == 'Active') {
                echo "<span style='background-color: #dcf5dc; color: #2eaa4c; padding: 3px; border-radius: 3px;font-size:12px'>Active</span>";
            } elseif ($status == 'Inactive') {
                echo "<span style='background-color: #fddddd; color: #e03b3b; padding: 3px; border-radius: 3px;font-size:12px'>Inactive</span>";
            } else {
                echo "<span style='padding: 5px; border-radius: 3px;'>$status</span>"; // fallback
            }
            ?>
            </td>

            <td style="display: flex; justify-content: center; gap: .5rem">
            <a href="Dashboard.php?delete=<?= $rowof['ID'] ?>"><i style="color: gray; font-size: .9rem" class="fas fa-trash"></i></a>
            <a href="Dashboard.php?edit=<?= $rowof['ID'] ?>" style="color:gray"><i class="fas fa-pencil-alt"></i></a>

            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
            </div>
        </div>
    </div>
</div>
                    
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
      <form action="Dashboard.php" method="POST" enctype="multipart/form-data">
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
    <form action="Dashboard.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="edit_id" value="<?= $editOfficialData['ID'] ?>">

      <div class="modal-content rounded-4 shadow">
        <div class="modal-header text-white bg-primary">
          <h5 class="modal-title">Edit Official</h5>
          <a href="Dashboard.php" class="btn-close btn-close-white" aria-label="Close"></a>
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
          <a href="Dashboard.php" class="btn btn-danger">Cancel</a>
          <button type="submit" name="update_official" class="btn btn-primary">Update Official</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Edit Official Modal END -->

<script src="Linegraph.js"></script>
<script src="Piegraph.js"></script>
 <script src="Graph.js"></script>
  <!-- js to preview image select on add Official start -->
        <script>
            document.getElementById('profileInput').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profilePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });
        </script>
  <!-- js to preview image select on add Official end -->

    <!-- js to preview image select on edit Official start -->
        <script>
        document.getElementById('profileInput2').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview2').src = e.target.result;
            };
            reader.readAsDataURL(file);
            }
        });
        </script>
  <!-- js to preview image select on edit Official end -->

  <!-- for aos start -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
  AOS.init();
</script>
 <!-- for aos end -->




<?php include ('includes/script.php');


?>
<?php ob_end_flush();  ?>