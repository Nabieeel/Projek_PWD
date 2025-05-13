<?php
include 'koneksi.php';
session_start();

$message = [];

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; 

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die(mysqli_error($conn));
    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
        if(password_verify($pass, $row['password'])){ 
            $_SESSION['user_id'] = $row['id'];
            header('location:login.php');
            exit();
        }else{
            $message[] = 'incorrect email or password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="CSS/registerr.css" />
</head>
<body>
  <div class="container">

    <form action="" method="post" class="login-form" novalidate>
      <h2>Login</h2>
      <?php
        if(isset($message)){
        foreach($message as $msg){
        echo '<div class = "message">'.$msg.'</div>';
        }
        }
      ?>

      <div class="input-group">
        <input type="email" name="email" required placeholder=" " />
        <label>Email</label>
      </div>

      <div class="input-group">
        <input type="password" name="password" required placeholder=" " />
        <label>Password</label>
      </div>

      <button type="submit" name="submit">Login</button>

      <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
  </div>
</body>
</html>
