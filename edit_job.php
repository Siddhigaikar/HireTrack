
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

$job_id = $_GET['id'];

$sql = $conn->prepare("select * from jobs where id=? and employer_id=?");
$sql->bind_param('ii',$job_id,$employer_id);
$sql->execute();
$result = $sql->get_result();
$job = $result->fetch_assoc();

if(!$job){
    die("Job Not Found");
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $job_title = $_POST['job_title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $salary = $_POST['salary'];
    $qualification = $_POST['qualification'];
    $status = $_POST['status'];

    $image = $job['image'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        $image_tmp = $_FILES['image']['tmp_name'];
        $image_info = getimagesize($image_tmp);

        if($image_info !== false){

            $image = time() . "_" . basename($_FILES['image']['name']);

            if(move_uploaded_file($image_tmp,"uploads/".$image)){

                if($job['image'] != "" && file_exists("uploads/".$job['image'])){
                    unlink("uploads/".$job['image']);
                }

            }
            else{
                die("Image Upload Failed");
            }

        }
        else{
            die("Please select a valid image.");
        }
    }

    $update = $conn->prepare("update jobs set job_title=?,description=?,location=?,job_type=?,salary=?,qualification=?,status=?,image=? where id=? and employer_id=?");

    $update->bind_param('ssssssssii',$job_title,$description,$location,$job_type,$salary,$qualification,$status,$image,$job_id,$employer_id);

    if($update->execute()){
        header("Location:dashboard.php");
        exit();
    }
    else{
        echo "Job Not Updated";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - Edit Job</title>
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

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Company Profile</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="add_job.php">Add Job</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="view_applications.php">Applications</a>
                </li>

            </ul>

            <div class="d-flex gap-2 align-items-center">
                <span class="text-white">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>

        </div>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="text-center">Edit Job</h2>
    <p class="text-center text-muted">Update your job details</p>

    <div class="row justify-content-center mt-4">

        <div class="col-md-8 col-lg-7">

            <div class="card">
                <div class="card-body p-4">

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($job['job_title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Description</label>
                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($job['location']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Type</label>
                            <select name="job_type" class="form-select" required>
                                <option value="Full Time" <?php if($job['job_type']=="Full Time") echo "selected"; ?>>Full Time</option>
                                <option value="Part Time" <?php if($job['job_type']=="Part Time") echo "selected"; ?>>Part Time</option>
                                <option value="Internship" <?php if($job['job_type']=="Internship") echo "selected"; ?>>Internship</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="text" name="salary" class="form-control" value="<?php echo htmlspecialchars($job['salary']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($job['qualification']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Active" <?php if($job['status']=="Active") echo "selected"; ?>>Active</option>
                                <option value="Inactive" <?php if($job['status']=="Inactive") echo "selected"; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Job Image</label>
                            <br>
                            <?php if($job['image'] != ""){ ?>
                                <img src="uploads/<?php echo htmlspecialchars($job['image']); ?>" width="200" class="mb-2">
                            <?php } ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Change Job Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Job</button>

                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>

                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
