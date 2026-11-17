
<?php
include "db.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != "candidate"){
    header("Location:login.php");
    exit();
}

$id = $_SESSION['id'];

if(isset($_GET['delete'])){

    $job_id = $_GET['delete'];

    $sql = $conn->prepare("delete from saved_jobs where job_id=? and candidate_id=?");
    $sql->bind_param('ii',$job_id,$id);

    if($sql->execute()){
        header("Location:saved_jobs.php");
        exit();
    }
}

$sql = $conn->prepare("select saved_jobs.id,jobs.id as job_id,jobs.job_title,jobs.description,jobs.location,jobs.job_type,jobs.salary,employer.company_name from saved_jobs join jobs on saved_jobs.job_id=jobs.id join employer on jobs.employer_id=employer.id where saved_jobs.candidate_id=? order by saved_jobs.id desc");

$sql->bind_param('i',$id);
$sql->execute();

$result = $sql->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - Saved Jobs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            <a class="navbar-brand" href="dashboard.php">HireTrack</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">

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
                        <a class="nav-link active" href="saved_jobs.php">Saved Jobs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">My Profile</a>
                    </li>

                </ul>

                <span class="text-white me-3">
                    Hello, <?php echo $_SESSION['name']; ?>
                </span>

                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>

            </div>
        </div>
    </nav>

    <div class="container mt-5">

        <div class="text-center mb-4">

            <h2>My Saved Jobs</h2>

            <p class="text-muted">
                View and manage the jobs you have saved for later.
            </p>

        </div>

        <div class="row">

            <?php
            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){
            ?>

                <div class="col-md-6 mb-4">

                    <div class="card h-100">

                        <div class="card-body p-4">

                            <h5>
                                <?php echo $row['job_title']; ?>
                            </h5>

                            <p class="text-primary">
                                <?php echo $row['company_name']; ?>
                            </p>

                            <p>
                                <?php echo $row['description']; ?>
                            </p>

                            <hr>

                            <p>
                                <b>Location:</b> <?php echo $row['location']; ?>
                            </p>

                            <p>
                                <b>Job Type:</b> <?php echo $row['job_type']; ?>
                            </p>

                            <p>
                                <b>Salary:</b> <?php echo $row['salary']; ?>
                            </p>

                            <hr>

                            <a href="apply_job.php?id=<?php echo $row['job_id']; ?>" class="btn btn-primary btn-sm">
                                Apply Now
                            </a>

                            <a href="saved_jobs.php?delete=<?php echo $row['job_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this saved job?')">
                                Remove
                            </a>

                        </div>

                    </div>

                </div>

            <?php
                }

            }
            else{
            ?>

                <div class="col-12">

                    <div class="card">
                        <div class="card-body text-center p-5">

                            <h5>No Saved Jobs Found</h5>

                            <p class="text-muted">
                                You haven't saved any jobs yet.
                            </p>

                            <a href="view_jobs.php" class="btn btn-primary btn-sm">
                                Browse Jobs
                            </a>

                        </div>
                    </div>

                </div>

            <?php
            }
            ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
