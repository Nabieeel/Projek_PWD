<?php
include 'koneksi.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('location:login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if(isset($_GET['logout'])){
    unset($_SESSION['user_id']);
    session_destroy();
    header('location:login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>home</title>
    <link rel="stylesheet" href="CSS/home.css" />
</head>
<body>
    <div class="container">
        <div class="profil">
            <?php
            $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = $user_id") or die('query failed');
            if(mysqli_num_rows($select) > 0){
                $fetch = mysqli_fetch_assoc($select);
            } else {
                header('location:login.php');
                exit;
            }

            if($fetch['image'] == ''){
                echo '<img src="images/default-avatar.png">';
            } else {
                echo '<img src="uploaded_img/'.$fetch['image'].'">';
            }
            ?>
            <h3><?php echo htmlspecialchars($fetch['name']); ?></h3>
            <a href="update_profil.php" class="btn">update profil</a>
            <br><br>
            <a href="home.php?logout=1" class="delete-btn">logout</a>
        </div>
    </div>
</body>
</html>
