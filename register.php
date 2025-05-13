<?php
include 'koneksi.php';

$message = [];

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $gender = mysqli_real_escape_string($conn,$_POST['gender']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $pass =  $_POST["password"];
    $cpass = $_POST['cpassword'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        $message[] = 'user already exist!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'confirm password not matched!';
        } elseif ($image_size > 2000000) { 
            $message[] = 'image size is too large!';
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn, "INSERT INTO `user_form` (name, gender, email, password, image) VALUES ('$name', '$gender', '$email', '$hashed_pass', '$image')") or die('Query insert gagal');

            if ($insert) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'registered succesfully!';
            } else {
                $message[] = 'registeration failed!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="CSS/registerr.css">
</head>
<body>
<div class="container">
  <form action="" method="post" enctype="multipart/form-data" class="login-form">
  <h2>Register</h2>
  <?php
  if(isset($message)){
    foreach($message as $msg){
      echo '<div class = "message">'.$msg.'</div>';
    }
  }
  ?>
  <div class="flex">
  <div class="input-group">
    <input type="text" name="name" required placeholder=" " />
    <label>Username</label>
  </div>
</div>
<div class="flex">
  <div class="vertical-group">
  <div class="input-group">
    <input type="text" name="gender" required placeholder=" " />
    <label>Gender(male/female)</label>
  </div>
  <div class="input-group">
    <input type="email" name="email" required placeholder=" ">
    <label>Email</label>
  </div>
</div>
</div>
  <div class="flex">
    <div class="vertical-group">
  <div class="input-group">
    <input type="password" name="password" required placeholder=" ">
    <label>Password</label>
  </div>
  <div class="input-group">
    <input type="password" name="cpassword" required placeholder=" ">
    <label>Confirm Password</label>
  </div>
  <div class="input-group">
    <input type="file" name="image" id="image" accept="image/jpeg, image/png, image/jpg" >
  </div>
</div>
</div>
  <button type="submit" name="submit">Register</button>

  <p>Already have an account? <a href="login.php">Login</a></p>
</form>
</div>
</body>
</html>
