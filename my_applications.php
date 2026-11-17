
<?php
include "db.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != "candidate"){
    header("Location:login.php");
    exit();
}

$id = $_SESSION['id'];

$sql = $conn->prepare("select applications.id,applications.status,applications.applied_at,jobs.job_title,jobs.location,jobs.salary,employer.company_name from applications join jobs on applications.job_id=jobs.id join employer on jobs.employer_id=employer.id where applications.candidate_id=? order by applications.id desc");

$sql->bind_param('i',$id);
$sql->execute();

$result = $sql->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - My Applications</title>
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
                        <a class="nav-link active" href="my_applications.php">My Applications</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="saved_jobs.php">Saved Jobs</a>
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

            <h2>My Applications</h2>

            <p class="text-muted">
                Keep track of all your job applications and their current status.
            </p>

        </div>

        <div class="card">

            <div class="card-body p-4">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Salary</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            if($result->num_rows > 0){

                                while($row = $result->fetch_assoc()){

                                    $statusClass = "bg-primary";

                                    if($row['status'] == "Pending"){
                                        $statusClass = "bg-warning text-dark";
                                    }
                                    elseif($row['status'] == "Shortlisted"){
                                        $statusClass = "bg-info text-dark";
                                    }
                                    elseif($row['status'] == "Rejected"){
                                        $statusClass = "bg-danger";
                                    }
                                    elseif($row['status'] == "Selected"){
                                        $statusClass = "bg-success";
                                    }
                            ?>

                                <tr>

                                    <td><?php echo $row['job_title']; ?></td>

                                    <td><?php echo $row['company_name']; ?></td>

                                    <td><?php echo $row['location']; ?></td>

                                    <td><?php echo $row['salary']; ?></td>

                                    <td><?php echo date("d M Y",strtotime($row['applied_at'])); ?></td>

                                    <td>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php
                                }

                            }
                            else{
                            ?>

                                <tr>
                                    <td colspan="6" class="text-center p-4">

                                        <h5>No Applications Found</h5>

                                        <p class="text-muted">
                                            You haven't applied for any jobs yet.
                                        </p>

                                        <a href="view_jobs.php" class="btn btn-primary btn-sm">
                                            Browse Jobs
                                        </a>

                                    </td>
                                </tr>

                            <?php
                            }
                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
