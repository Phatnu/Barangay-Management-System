<?php
require_once("../connections/connection.php"); // use require_once to avoid duplicate loading
$con = connection();

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design

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
</style>
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
           <img src="../admin/barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>"
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
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->

              <li class="nav-item">
                <a class="nav-link" href="Resident_information.php">
                  <i class="fas fa-user-friends"></i>
                    <span>Resident Information</span></a>
            </li>
                                      <li class="nav-item">
                <a class="nav-link" href="Blotter_records.php">
                <i class="fas fa-file-alt"></i>
                    <span>Blotter Records</span></a>
            </li>
              <li class="nav-item">
                <a class="nav-link" href="calendar.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Calendar</span></a>
            </li>
                         <li class="nav-item">
                <a class="nav-link" href="CertificateOfIndigency.php">
                    <i class="fas fa-file-signature"></i>
                    <span>Certificate Of Indigency</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Business_Permit.php">
                  <i class="fas fa-briefcase"></i>
                    <span>Business Permit</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="BarangayCertificate.php">
                <i class="fas fa-file-alt"></i>
                    <span>Barangay Certificate</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>



        </ul>
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

