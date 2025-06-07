<?php

include_once("connections/connection.php");
$con = connection();

if(isset($_POST['submit'])){

    $file_name = $_FILES['image']['name'];
    $tempname = $_FILES['image']['tmp_name'];
    $folder = 'images/'.$file_name;

    $query = mysqli_query($con,"INSERT INTO STAFF (PROFILE) VALUES ('$file_name')");

    if(move_uploaded_file($tempname,$folder)){
        header ("location:upload.php");
    }
    else{
        echo "NOT UPLOAD";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="image">
    <br>
    <button type="submit" name="submit">SUBMIT</button>
    </form>
    <div>
        <?php $res = mysqli_query($con,"SELECT * FROM STAFF ");
        while($row = mysqli_fetch_assoc($res)){
        ?>

        <img src="Images/<?=$row['PROFILE']?>" alt="">
        <?php  }?>
    </div>
</body>
</html>