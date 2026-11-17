
<?php
include "db.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = $conn->prepare("select id,name,email,password,role from users where email=?");

    $sql->bind_param('s',$email);
    $sql->execute();

    $sql->bind_result($id,$name,$dbemail,$password,$role);
    $sql->fetch();

    if($password && password_verify($pass,$password)){

        $_SESSION['id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $dbemail;
        $_SESSION['role'] = $role;

        header("Location:dashboard.php");
        exit();

    }
    else{
        echo "Invalid Email or Password";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container col-md-5 mt-5 shadow rounded p-4 bg-white">

        <h2 class="text-center mb-4">Login to HireTrack</h2>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>

            <p class="text-center mt-3">
                Don't have an account?
                <a href="register.php">Register here</a>
            </p>

        </form>

    </div>

</body>
</html>
