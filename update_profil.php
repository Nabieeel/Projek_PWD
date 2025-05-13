<?php
include 'koneksi.php';
session_start();

// memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = [];

// Ambil data user dari database untuk ditampilkan di form
$select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
if(mysqli_num_rows($select) > 0){
    $fetch = mysqli_fetch_assoc($select);
} else {
    die('User not found');
}

// Proses hapus akun
if (isset($_POST['delete_account'])) {
    $delete = mysqli_query($conn, "DELETE FROM `user_form` WHERE id = '$user_id'") or die('query failed');

    if ($delete) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    } else {
        $message[] = 'Failed to delete account!';
    }
}

if(isset($_POST['update_profil'])){
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_gender = mysqli_real_escape_string($conn, $_POST['update_gender']);

    // Update username
    $update_name = mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name' WHERE id = '$user_id'");
    if ($update_name) {
    $message[] = 'Username updated successfully!';
    } else {
    $message[] = 'Failed to update username!';
    }

    // Update gender
    $update_gender = mysqli_query($conn, "UPDATE `user_form` SET gender = '$update_gender' WHERE id = '$user_id'");
    if ($update_gender) {
    $message[] = 'Gender updated successfully!';
    } else {
    $message[] = 'Failed to update gender!';
    }

    // Ambil password lama untuk validasi
    $select_pass = mysqli_query($conn, "SELECT password FROM `user_form` WHERE id = '$user_id'") or die('query failed');
    $row = mysqli_fetch_assoc($select_pass);
    $hashed_old_pass = $row['password'];

    $old_pass = $_POST['update_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // Update password jika diisi
    if(!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)){
        if(!password_verify($old_pass, $hashed_old_pass)){
            $message[] = 'Old password not matched!';
        } elseif($new_pass !== $confirm_pass){
            $message[] = 'Confirm password not matched!';
        } else {
            $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE `user_form` SET password = '$new_hashed_pass' WHERE id = '$user_id'") or die('query failed');
            $message[] = 'Password updated successfully!';
        }
    }

    // Update gambar profil jika ada upload
    if(isset($_FILES['update_image']) && $_FILES['update_image']['name'] != ''){
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/'.$update_image;

        if($update_image_size > 2000000){
            $message[] = 'Image is too large!';
        } else {
            $update_image_query = mysqli_query($conn, "UPDATE user_form SET image = '$update_image' WHERE id = '$user_id'") or die('query failed');
            if($update_image_query){
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
                $message[] = 'Image updated successfully!';
            }
        }

    }

    // Refresh data user setelah update
    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
    if(mysqli_num_rows($select) > 0){
        $fetch = mysqli_fetch_assoc($select);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Update Profile</title>
    <link rel="stylesheet" href="CSS/update_profile.css" />
</head>
<body>
    <div class="update-profil">
        <?php
        if(!empty($message)){
            foreach($message as $msg){
                echo '<p class="message">'.$msg.'</p>';
            }
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <?php
            if(!empty($fetch['image'])){
                echo '<img src="uploaded_img/'.$fetch['image'].'" alt="Profile Image">';
            } else {
                echo '<img src="images/default-avatar.png" alt="Default Avatar">';
            }
            ?>
            <div class="flex">
                <div class="input-group">
                    <span>Username:</span>
                    <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" placeholder="Enter new username" class="box" required />
                    <span>Gender (male/female):</span>
                    <input type="text" name="update_gender" value="<?php echo $fetch['gender']; ?>" placeholder="Enter new gender" class="box" required />
                    <span>Update your picture:</span>
                    <input type="file" name="update_image" accept="image/jpeg, image/png" class="box" />
                </div>
                <div class="input-group">
                    <span>Old password:</span>
                    <input type="password" name="update_pass" placeholder="Enter previous password" class="box" />
                    <span>New password:</span>
                    <input type="password" name="new_pass" placeholder="Enter new password" class="box" />
                    <span>Confirm password:</span>
                    <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box" />
                </div>
            </div>
            <input type="submit" value="Update Profile" name="update_profil" class="btn" />
            <input type="submit" value="Delete Account" name="delete_account" class="delete-btn" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');" />
            <br>
            <br>
            <a href="index.html" class="delete-btn">Go Back</a>
        </form>
    </div>
</body>
</html>
