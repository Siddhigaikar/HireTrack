
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

    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    if(in_array($status,['Pending','Shortlisted','Rejected','Selected'])){

        $sql = $conn->prepare("update applications join jobs on applications.job_id=jobs.id set applications.status=? where applications.id=? and jobs.employer_id=?");

        $sql->bind_param('sii',$status,$application_id,$employer_id);

        if($sql->execute()){
            header("Location:view_applications.php");
            exit();
        }
        else{
            echo "Status Not Updated";
        }
    }
}

$sql = $conn->prepare("select applications.id,applications.resume,applications.status,applications.applied_at,jobs.job_title,users.name,users.email from applications join jobs on applications.job_id=jobs.id join users on applications.candidate_id=users.id where jobs.employer_id=? order by applications.id desc");

$sql->bind_param('i',$employer_id);
$sql->execute();

$result = $sql->get_result();
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>HireTrack - View Applications</title>
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
                        <a class="nav-link" href="add_job.php">Add Job</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="view_applications.php">Applications</a>
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
        <h2 class="fw-bold">Job Applications</h2>
        <p class="text-muted">View candidate applications and manage their application status.</p>
    </div>

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped bg-white align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Job Title</th>
                            <th>Resume</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        if($result->num_rows > 0){

                            while($row = $result->fetch_assoc()){
                        ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>

                                <td><?php echo htmlspecialchars($row['email']); ?></td>

                                <td><?php echo htmlspecialchars($row['job_title']); ?></td>

                                <td>
                                    <?php
                                    if($row['resume'] != ""){
                                    ?>
                                        <a href="uploads/<?php echo rawurlencode(basename($row['resume'])); ?>" target="_blank" class="btn btn-primary btn-sm">View Resume</a>
                                    <?php
                                    }
                                    else{
                                        echo "No Resume";
                                    }
                                    ?>
                                </td>

                                <td><?php echo $row['applied_at']; ?></td>

                                <td><?php echo $row['status']; ?></td>

                                <td>

                                    <form method="POST">

                                        <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">

                                        <select name="status" class="form-select mb-2">

                                            <option value="Pending" <?php if($row['status']=="Pending") echo "selected"; ?>>Pending</option>

                                            <option value="Shortlisted" <?php if($row['status']=="Shortlisted") echo "selected"; ?>>Shortlisted</option>

                                            <option value="Rejected" <?php if($row['status']=="Rejected") echo "selected"; ?>>Rejected</option>

                                            <option value="Selected" <?php if($row['status']=="Selected") echo "selected"; ?>>Selected</option>

                                        </select>

                                        <button type="submit" class="btn btn-success btn-sm">Update</button>

                                    </form>

                                </td>
                            </tr>

                        <?php
                            }

                        }
                        else{
                            echo "<tr><td colspan='7' class='text-center'>No Applications Found</td></tr>";
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
