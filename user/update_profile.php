<?php
session_start();
include ('../connections/connection.php');

$con = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staffid = $_POST['staffid'];
    $first_name =  $_POST['first_name'];
    $last_name =  $_POST['last_name'];
    $gender = $_POST['gender'];
    $email =  $_POST['email'];
    $birth_day = $_POST['birth_day'];
    $position = $_POST['position'];


    // Profile Picture Upload Handling
    if (!empty($_FILES['profile']['name'])) {
        $target_dir = "../admin/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Ensure the folder exists
        }

        $imageFileType = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        // Validate File Type & Size
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Allowed: JPG, JPEG, PNG.";
            header("Location: profile.php");
            exit();
        }

        if ($_FILES['profile']['size'] > 5000000) { // 5MB limit
            $_SESSION['error'] = "File size is too large.";
            header("Location: profile.php");
            exit();
        }

        $check = getimagesize($_FILES['profile']['tmp_name']);
        if ($check === false) {
            $_SESSION['error'] = "File is not a valid image.";
            header("Location: profile.php");
            exit();
        }

        // Generate Unique Filename
        $newFileName = "profile_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $newFileName;

        // Move Uploaded File
        if (move_uploaded_file($_FILES['profile']['tmp_name'], $target_file)) {
            // Update Profile Image in Database
            $stmt = $con->prepare("UPDATE STAFF SET PROFILE=? WHERE STAFFID=?");
            $stmt->bind_param("si", $newFileName, $staffid);
            if (!$stmt->execute()) {
                $_SESSION['error'] = "Error updating profile image: " . $stmt->error;
                error_log("DB Error: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Failed to upload file.";
            header("Location: profile.php");
            exit();
        }
    }

    // Update Other User Details
    $sql = "UPDATE STAFF SET FIRST_NAME='$first_name', LAST_NAME='$last_name', GENDER='$gender',EMAIL='$email', BIRTH_DAY='$birth_day', USERTYPE='$position' WHERE STAFFID='$staffid'";

    if ($con->query($sql) === TRUE) {
        $_SESSION['success'] = "Profile updated successfully!";
        $_SESSION['toastr'] = "toastr.success('Profile updated successfully!');";
    } else {
        $_SESSION['error'] = "Error updating profile: " . $con->error;
        $_SESSION['toastr'] = "toastr.error('Error updating profile');";
    }

    header("Location: profile.php");
    exit();
}
?>
