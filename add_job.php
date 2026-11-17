
<?php
include "db.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != "employer"){
    header("Location:login.php");
    exit();
}

$id = $_SESSION['id'];

$check = $conn->prepare("select id from employer where user_id=?");
$check->bind_param('i',$id);
$check->execute();
$check->bind_result($employer_id);
$check->fetch();
$check->close();

if($_SERVER["REQUEST_METHOD"] === "POST"){

    if(!$employer_id){
        echo "Please complete your company profile first.";
    }
    else{

        $job_title = $_POST['job_title'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $job_type = $_POST['job_type'];
        $salary = $_POST['salary'];
        $qualification = $_POST['qualification'];

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];

            $image_info = getimagesize($image_tmp);

            if($image_info !== false){

                $image = time() . "_" . basename($image_name);

                if(move_uploaded_file($image_tmp, "uploads/".$image)){

                    $sql = $conn->prepare("insert into jobs (employer_id,job_title,description,location,job_type,salary,qualification,image) values (?,?,?,?,?,?,?,?)");

                    $sql->bind_param('isssssss',$employer_id,$job_title,$description,$location,$job_type,$salary,$qualification,$image);

                    if($sql->execute()){
                        echo "Job Posted Successfully";
                    }
                    else{
                        echo "Job Not Posted";
                    }
                }
                else{
                    echo "Image Upload Failed";
                }

            }
            else{
                echo "Please select a valid image.";
            }

        }
        else{
            echo "Please select a job image.";
        }
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>HireTrack - Add Job</title>
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
                        <a class="nav-link" href="profile.php">My Profile</a>
                    </li>
                </ul>

            <?php } elseif($_SESSION['role'] == "employer"){ ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Company Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_job.php">Add Job</a>
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

<div class="container min-vh-100 d-flex flex-column justify-content-center">

    <h2 class="text-center">Post a New Job</h2>

    <p class="text-center text-muted">
        Share a new opportunity with candidates looking for jobs.
    </p>

    <div class="row justify-content-center mt-3">

        <div class="col-md-8 col-lg-7">

            <div class="card">

                <div class="card-body p-4">

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="job_title" class="form-control" placeholder="Enter job title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter job description" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Enter job location" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Type</label>
                            <select name="job_type" class="form-select" required>
                                <option value="">Select Job Type</option>
                                <option value="Full Time">Full Time</option>
                                <option value="Part Time">Part Time</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="text" name="salary" class="form-control" placeholder="Enter salary" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control" placeholder="Enter required qualification" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Post Job</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
