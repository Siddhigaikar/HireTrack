
<?php
include "db.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = $conn->prepare("insert into users (name,email,password,role) values (?,?,?,?)");

    $sql->bind_param('ssss', $name, $email, $pass, $role);

    if($sql->execute()){
        header("Location:login.php");
        exit();
    }
    else{
        echo "Registration Failed: " . $sql->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>HireHub - Register</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container col-md-5 mt-5 shadow rounded p-4 bg-white">

        <h2 class="text-center mb-4">Register with HireHub</h2>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Register As</label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="candidate">Candidate</option>
                    <option value="employer">Employer</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>

            <p class="text-center mt-3">
                Already have an account?
                <a href="login.php">Login here</a>
            </p>

        </form>

    </div>

</body>
</html>
