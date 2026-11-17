
<?php
include "db.php";

if(!isset($_SESSION['id'])){
    header("Location:login.php");
    exit();
}

$id = $_SESSION['id'];
$role = $_SESSION['role'];

if($role == "candidate"){

    $check = $conn->prepare("select id from candidate_profile where user_id=?");
    $check->bind_param('i',$id);
    $check->execute();
    $check->store_result();

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $phone = $_POST['phone'];
        $qualification = $_POST['qualification'];
        $skills = $_POST['skills'];
        $experience = $_POST['experience'];

        $resume = "";

        if(isset($_FILES['resume']) && $_FILES['resume']['name'] != ""){

            $resume = time() . "_" . $_FILES['resume']['name'];

            move_uploaded_file($_FILES['resume']['tmp_name'],"uploads/".$resume);
        }

        if($check->num_rows > 0){

            if($resume != ""){
                $sql = $conn->prepare("update candidate_profile set phone=?,qualification=?,skills=?,experience=?,resume=? where user_id=?");
                $sql->bind_param('sssssi',$phone,$qualification,$skills,$experience,$resume,$id);
            }
            else{
                $sql = $conn->prepare("update candidate_profile set phone=?,qualification=?,skills=?,experience=? where user_id=?");
                $sql->bind_param('ssssi',$phone,$qualification,$skills,$experience,$id);
            }

        }
        else{
            $sql = $conn->prepare("insert into candidate_profile (user_id,phone,qualification,skills,experience,resume) values (?,?,?,?,?,?)");
            $sql->bind_param('isssss',$id,$phone,$qualification,$skills,$experience,$resume);
        }

        if($sql->execute()){
            echo "Profile Saved Successfully";
        }
        else{
            echo "Profile Not Saved";
        }
    }

    $check->close();
}

elseif($role == "employer"){

    $check = $conn->prepare("select id from employer where user_id=?");
    $check->bind_param('i',$id);
    $check->execute();
    $check->store_result();

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $company_name = $_POST['company_name'];
        $company_description = $_POST['company_description'];
        $location = $_POST['location'];

        if($check->num_rows > 0){

            $sql = $conn->prepare("update employer set company_name=?,company_description=?,location=? where user_id=?");
            $sql->bind_param('sssi',$company_name,$company_description,$location,$id);

        }
        else{

            $sql = $conn->prepare("insert into employer (user_id,company_name,company_description,location) values (?,?,?,?)");
            $sql->bind_param('isss',$id,$company_name,$company_description,$location);

        }

        if($sql->execute()){
            echo "Company Profile Saved Successfully";
        }
        else{
            echo "Profile Not Saved";
        }
    }

    $check->close();
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>HireTrack - My Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="dashboard.php">💼 HireTrack</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">

            <?php if($_SESSION['role'] == "candidate"){ ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_jobs.php">Browse Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_applications.php">My Applications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="saved_jobs.php">Saved Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">My Profile</a>
                    </li>
                </ul>

            <?php } elseif($_SESSION['role'] == "employer"){ ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">Company Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_job.php">Add Job</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_applications.php">Applications</a>
                    </li>
                </ul>

            <?php } else { ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_jobs.php">Job Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_applications.php">Applications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">Reports</a>
                    </li>
                </ul>

            <?php } ?>

            <div class="d-flex gap-2 align-items-center">
                <span class="text-white">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>

        </div>
    </div>
</nav>

<div class="container mt-4">

    <div class="text-center mb-4">

        <h2>Manage Profile</h2>

        <p class="text-muted">
            Update your personal and professional information.
        </p>

    </div>

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card">

                <div class="card-body p-4">

                    <?php
                    if($role == "candidate"){
                    ?>

                        <h5 class="mb-4">Candidate Profile</h5>

                        <form method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Skills</label>
                                <textarea name="skills" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Experience</label>
                                <input type="text" name="experience" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Resume</label>
                                <input type="file" name="resume" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Save Profile
                            </button>

                        </form>

                    <?php
                    }
                    elseif($role == "employer"){
                    ?>

                        <h5 class="mb-4">Company Profile</h5>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Company Description</label>
                                <textarea name="company_description" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Save Profile
                            </button>

                        </form>

                    <?php
                    }
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
