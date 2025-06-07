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
                                 <img class="img-profile rounded-circle" src="../admin/images/<?=$row['PROFILE']?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../user/Profile.php">
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-0">
                        <div style="display: block;">
                        <h1 class="h3 mb-0 text-gray-800" style="font-weight: bold;color:black">Dashboard Overview</h1>
                        <p style="color: gray;">Welcome back, Admin!</p>
                        </div>
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


                    
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

   

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->





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