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


if (isset($_POST['change-password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch stored password
    $sqlquery = "SELECT PASSWORD FROM STAFF WHERE STAFFID = '$user_id'";
    $result = mysqli_query($con, $sqlquery);
    $rows = $result->fetch_assoc();

    if ($rows['PASSWORD'] === md5($current_password)) {  
        if ($new_password === $confirm_password) {
            $hashed_password = md5($new_password);
            $sqlupdate = "UPDATE STAFF SET PASSWORD = '$hashed_password' WHERE STAFFID = '$user_id'";
            if (mysqli_query($con, $sqlupdate)) {
                $_SESSION['toastr'] = "toastr.success('Password changed successfully!');";
            } else {
                $_SESSION['toastr'] = "toastr.error('Error updating password. Please try again.');";
            }
        } else {
            $_SESSION['toastr'] = "toastr.warning('New password and confirm password do not match.');";
        }
    } else {
        $_SESSION['toastr'] = "toastr.error('Current password is incorrect.');";
    }

    // **Redirect to prevent form resubmission**
    header("Location: change_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <!-- Toastr CSS START -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
 <!-- Toastr CSS END-->
</head>
<style>

        .password-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            border-left: 3px solid #283593;
        }

        .form-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
            text-align: center;
    
        }

        .field-group {
            margin-bottom: 24px;
        }

        .field-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 8px;
        }

        .password-input {
            width: 100%;
            height: 48px;
            padding: 0 48px 0 16px;
            font-size: 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }

        .password-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .password-input::placeholder {
            color: #9ca3af;
        }

        .eye-button {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            transition: color 0.2s;
        }

        .eye-button:hover {
            color: #6b7280;
        }

        .strength-indicator {
            display: none;
            margin-top: 8px;
        }

        .strength-indicator.visible {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .strength-bars {
            flex: 1;
            display: flex;
            gap: 4px;
            height: 4px;
        }

        .strength-bar {
            flex: 1;
            height: 100%;
            background-color: #f3f4f6;
            border-radius: 2px;
            transition: background-color 0.3s ease;
        }

        .strength-label {
            font-size: 12px;
            font-weight: 500;
            color: #9ca3af;
            min-width: 45px;
            text-align: right;
        }

        /* Strength levels */
        .strength-weak .strength-bar:nth-child(1) {
            background-color: #ef4444;
        }
        .strength-weak .strength-label {
            color: #ef4444;
        }

        .strength-medium .strength-bar:nth-child(1),
        .strength-medium .strength-bar:nth-child(2) {
            background-color: #f59e0b;
        }
        .strength-medium .strength-label {
            color: #f59e0b;
        }

        .strength-strong .strength-bar {
            background-color: #10b981;
        }
        .strength-strong .strength-label {
            color: #10b981;
        }

        .submit-button {
            width: 100%;
            height: 48px;
            background: #283593;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        .submit-button:hover {
            background: #1e237a;
            transform: translateY(-1px);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .password-container {
                padding: 24px;
                margin: 16px;
            }
            
            .form-title {
                font-size: 20px;
                margin-bottom: 24px;
            }
        }
</style>
<body>

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
                      <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <label class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$row['FIRST_NAME']?></label></span>
                      <img class="img-profile rounded-circle"
                          src="images/<?=$row['PROFILE']?>">
                  </a>
                  <!-- Dropdown - User Information -->
                  <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                      aria-labelledby="userDropdown">
                      <a class="dropdown-item" href="profile.php">
                          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                          Profile
                      </a>
                      <a class="dropdown-item" href="#">
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
       
<div class="d-flex justify-content-center align-items-center" style="height: 80vh; background-color: #f8f9fa;">

 <div class="password-container" style="margin-top: 2rem;">
        <h1 class="form-title">Change Password</h1>
        <p class="text-center">Enter a new password below to change your password</p>
        <hr>
        <form method="POST" autocomplete="off">
            <div class="field-group">
                <label class="field-label">Current Password</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        class="password-input" 
                        id="current-password" 
                        name="current_password" 
                        placeholder="••••••••••"
                        required 
                        autocomplete="current-password"
                    >
                    <button type="button" class="eye-button" onclick="toggleVisibility('current-password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">New Password</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        class="password-input" 
                        id="new-password" 
                        name="new_password" 
                        placeholder="••••••••••"
                        required 
                        autocomplete="new-password"
                    >
                    <button type="button" class="eye-button" onclick="toggleVisibility('new-password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="strength-indicator" id="new-strength">
                    <div class="strength-bars">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <span class="strength-label">Weak</span>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Confirm Password</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        class="password-input" 
                        id="confirm-password" 
                        name="confirm_password" 
                        placeholder="••••••••••"
                        required 
                        autocomplete="new-password"
                    >
                    <button type="button" class="eye-button" onclick="toggleVisibility('confirm-password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="strength-indicator" id="confirm-strength">
                    <div class="strength-bars">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <span class="strength-label">Weak</span>
                </div>
            </div>

            <button type="submit" name="change-password" class="submit-button">
                Change Password
            </button>
        </form>
    </div>
</div>





          </div>
        </div>
          <!-- End of Topbar -->


<!-- Scripts FOR TOAST START -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    <?php 
        if (!empty($_SESSION['toastr'])) {
            echo $_SESSION['toastr']; 
            unset($_SESSION['toastr']); // Clear session message after displaying
        }
    ?>
</script>
<!-- Scripts FOR TOAST END -->
<script>
        function toggleVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function getPasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;

            if (score <= 2) return { level: 'weak', text: 'Weak' };
            if (score <= 4) return { level: 'medium', text: 'So-so' };
            return { level: 'strong', text: 'Strong' };
        }

        function updateStrengthIndicator(inputId, indicatorId) {
            const input = document.getElementById(inputId);
            const indicator = document.getElementById(indicatorId);
            const label = indicator.querySelector('.strength-label');

            input.addEventListener('input', function() {
                const password = this.value;
                
                if (password.length === 0) {
                    indicator.classList.remove('visible');
                    indicator.className = 'strength-indicator';
                    return;
                }

                indicator.classList.add('visible');
                const strength = getPasswordStrength(password);
                
                indicator.className = `strength-indicator visible strength-${strength.level}`;
                label.textContent = strength.text;
            });
        }

        // Initialize strength checking
        updateStrengthIndicator('new-password', 'new-strength');
        updateStrengthIndicator('confirm-password', 'confirm-strength');
    </script>


</body>
</html>







<?php 
include ('includes/script.php');
// include ('includes/footer.php');
ob_end_flush();  
?>