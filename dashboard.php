
<?php
include "db.php";

if(!isset($_SESSION['id'])){
    header("Location:login.php");
    exit();
}

$totalUsers = $conn->query("select count(*) as total from users")->fetch_assoc()['total'];
$totalJobs = $conn->query("select count(*) as total from jobs where status='Active'")->fetch_assoc()['total'];
$totalApplications = $conn->query("select count(*) as total from applications")->fetch_assoc()['total'];

if($_SESSION['role'] == "candidate"){

    $candidate_id = $_SESSION['id'];

    $sql = $conn->prepare("select count(*) as total from applications where candidate_id=?");
    $sql->bind_param("i",$candidate_id);
    $sql->execute();
    $totalApplications = $sql->get_result()->fetch_assoc()['total'];

    $sql = $conn->prepare("select count(*) as total from saved_jobs where candidate_id=?");
    $sql->bind_param("i",$candidate_id);
    $sql->execute();
    $totalSaved = $sql->get_result()->fetch_assoc()['total'];

    $sql = $conn->prepare("select count(*) as total from applications where candidate_id=? and status='Pending'");
    $sql->bind_param("i",$candidate_id);
    $sql->execute();
    $totalPending = $sql->get_result()->fetch_assoc()['total'];

    $sql = $conn->prepare("select count(*) as total from applications where candidate_id=? and status='Shortlisted'");
    $sql->bind_param("i",$candidate_id);
    $sql->execute();
    $totalShortlisted = $sql->get_result()->fetch_assoc()['total'];

    $sql = $conn->prepare("select jobs.*, employer.company_name from jobs join employer on jobs.employer_id=employer.id where jobs.status='Active' order by jobs.created_at desc");
    $sql->execute();
    $jobs = $sql->get_result();
}

elseif($_SESSION['role'] == "employer"){

    $employer_id = $_SESSION['id'];

    $sql = $conn->prepare("select id from employer where user_id=?");
    $sql->bind_param("i",$employer_id);
    $sql->execute();
    $employer = $sql->get_result()->fetch_assoc();

    if($employer){

        $company_id = $employer['id'];

        $sql = $conn->prepare("select * from jobs where employer_id=? order by created_at desc");
        $sql->bind_param("i",$company_id);
        $sql->execute();
        $myJobs = $sql->get_result();

    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireTrack Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .job-card{
            border:none;
            border-radius:15px;
            transition:0.3s;
        }

        .job-card:hover{
            transform:translateY(-5px);
        }

        .stats-card{
            border:none;
            border-radius:15px;
        }

        .job-image{
            width:100%;
            height:190px;
            object-fit:cover;
        }
    </style>
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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

            <?php } else { ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
        <h2 class="fw-bold">Welcome to HireTrack</h2>
        <p class="text-muted">Find opportunities and manage your career</p>
    </div>

    <?php if($_SESSION['role'] == "candidate"){ ?>

        <h4 class="fw-bold mb-3"></h4>

        <div class="row g-4 mb-5">

            <div class="col-md-3 col-sm-6">
                <div class="card stats-card shadow text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Applications</h6>
                        <h2 class="fw-bold text-primary"><?php echo $totalApplications; ?></h2>
                        <p class="mb-0 text-muted">Jobs Applied</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stats-card shadow text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Saved Jobs</h6>
                        <h2 class="fw-bold text-success"><?php echo $totalSaved; ?></h2>
                        <p class="mb-0 text-muted">Jobs Bookmarked</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stats-card shadow text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Pending</h6>
                        <h2 class="fw-bold text-warning"><?php echo $totalPending; ?></h2>
                        <p class="mb-0 text-muted">Applications Pending</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card stats-card shadow text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Shortlisted</h6>
                        <h2 class="fw-bold text-info"><?php echo $totalShortlisted; ?></h2>
                        <p class="mb-0 text-muted">Applications Shortlisted</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="mb-4">
            <h4 class="fw-bold">Latest Job Opportunities</h4>
            <p class="text-muted">Explore the latest jobs posted by employers</p>
        </div>

        <div class="row g-4">

            <?php if($jobs->num_rows > 0){ ?>

                <?php while($row = $jobs->fetch_assoc()){ ?>

                    <div class="col-md-6 col-lg-4">
                        <div class="card job-card shadow h-100">

                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="job-image" alt="Job Image">

                            <div class="card-body">

                                <h5 class="fw-bold">
                                    <?php echo htmlspecialchars($row['job_title']); ?>
                                </h5>

                                <p class="text-primary fw-semibold mb-2">
                                    <?php echo htmlspecialchars($row['company_name']); ?>
                                </p>

                                <p class="text-muted">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Location:</strong>
                                    <?php echo htmlspecialchars($row['location']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Job Type:</strong>
                                    <?php echo htmlspecialchars($row['job_type']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Salary:</strong>
                                    <?php echo htmlspecialchars($row['salary']); ?>
                                </p>

                                <p class="mb-3">
                                    <strong>Qualification:</strong>
                                    <?php echo htmlspecialchars($row['qualification']); ?>
                                </p>

                                <div class="d-flex gap-2">
                                    <a href="apply_job.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                        Apply Now
                                    </a>

                                    <a href="save_job.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-success btn-sm">
                                        Save Job
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No jobs are available at the moment.
                    </div>
                </div>

            <?php } ?>

        </div>

    <?php } elseif($_SESSION['role'] == "employer"){ ?>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Total Users</h6>
                        <h2><?php echo $totalUsers; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Active Jobs</h6>
                        <h2><?php echo $totalJobs; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Total Applications</h6>
                        <h2><?php echo $totalApplications; ?></h2>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-5 mb-4">
            <h4 class="fw-bold">My Posted Jobs</h4>
            <p class="text-muted">Manage the jobs you have posted.</p>
        </div>

        <div class="row g-4">

            <?php if(isset($myJobs) && $myJobs->num_rows > 0){ ?>

                <?php while($row = $myJobs->fetch_assoc()){ ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="card job-card shadow h-100">

                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="job-image" alt="Job Image">

                            <div class="card-body">

                                <h5 class="fw-bold">
                                    <?php echo htmlspecialchars($row['job_title']); ?>
                                </h5>

                                <p class="mb-2">
                                    <strong>Location:</strong>
                                    <?php echo htmlspecialchars($row['location']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Job Type:</strong>
                                    <?php echo htmlspecialchars($row['job_type']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Salary:</strong>
                                    <?php echo htmlspecialchars($row['salary']); ?>
                                </p>

                                <p class="mb-3">
                                    <strong>Status:</strong>

                                    <?php if($row['status'] == "Active"){ ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php } ?>

                                </p>

                                <div class="d-flex gap-2">

                                    <a href="edit_job.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                        Edit
                                    </a>

                                    <a href="delete_job.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?')">
                                        Delete
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="col-12">

                    <div class="alert alert-info text-center">
                        You haven't posted any jobs yet.

                        <br>

                        <a href="add_job.php" class="btn btn-primary btn-sm mt-2">
                            Add Job
                        </a>
                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Total Users</h6>
                        <h2><?php echo $totalUsers; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Active Jobs</h6>
                        <h2><?php echo $totalJobs; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stats-card shadow text-center">
                    <div class="card-body">
                        <h6>Total Applications</h6>
                        <h2><?php echo $totalApplications; ?></h2>
                    </div>
                </div>
            </div>

        </div>

    <?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
