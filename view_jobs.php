
<?php
include "db.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != "candidate"){
    header("Location:login.php");
    exit();
}

$search = "";

if(isset($_GET['search'])){
    $search = $_GET['search'];
}

$sql = $conn->prepare("select jobs.*, employer.company_name from jobs join employer on jobs.employer_id=employer.id where jobs.status='Active' and (jobs.job_title like ? or jobs.location like ?) order by jobs.id desc");

$search_value = "%".$search."%";

$sql->bind_param('ss',$search_value,$search_value);
$sql->execute();

$result = $sql->get_result();
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>HireTrack - Browse Jobs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

        .search-box{
            border-radius:10px;
        }

        .job-info{
            color:#6c757d;
            font-size:14px;
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

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="view_jobs.php">Browse Jobs</a>
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

                <div class="d-flex gap-2 align-items-center">
                    <span class="text-white">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>

            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <div class="text-center mb-4">
            <h2 class="fw-bold">Explore Job Opportunities</h2>
            <p class="text-muted">Find the right opportunity for your career</p>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-3">

                <form method="GET">

                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search by job title or location..." value="<?php echo htmlspecialchars($search); ?>">

                        <button type="submit" class="btn btn-primary px-4">
                            Search
                        </button>

                        <a href="view_jobs.php" class="btn btn-secondary px-4">
                            Reset
                        </a>
                    </div>

                </form>

            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">Available Jobs</h4>
                <p class="text-muted mb-0">Discover jobs posted by employers</p>
            </div>
        </div>

        <div class="row g-4">

            <?php
            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){
            ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card job-card shadow h-100">

                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="job-image" alt="Job Image">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-2">
                                <?php echo htmlspecialchars($row['job_title']); ?>
                            </h5>

                            <h6 class="text-primary mb-3">
                                <?php echo htmlspecialchars($row['company_name']); ?>
                            </h6>

                            <p class="text-muted">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>

                            <hr>

                            <p class="job-info mb-2">
                                <strong>Location:</strong>
                                <?php echo htmlspecialchars($row['location']); ?>
                            </p>

                            <p class="job-info mb-2">
                                <strong>Job Type:</strong>
                                <?php echo htmlspecialchars($row['job_type']); ?>
                            </p>

                            <p class="job-info mb-2">
                                <strong>Salary:</strong>
                                <?php echo htmlspecialchars($row['salary']); ?>
                            </p>

                            <p class="job-info mb-3">
                                <strong>Qualification:</strong>
                                <?php echo htmlspecialchars($row['qualification']); ?>
                            </p>

                            <div class="d-flex gap-2">

                                <a href="apply_job.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm flex-fill">
                                    Apply Now
                                </a>

                                <a href="save_job.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-success btn-sm flex-fill">
                                    Save Job
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php
                }

            }
            else{
            ?>

                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center py-5">

                            <h5 class="fw-bold">No Jobs Found</h5>

                            <p class="text-muted mb-3">
                                We couldn't find any jobs matching your search.
                            </p>

                            <a href="view_jobs.php" class="btn btn-primary">
                                View All Jobs
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
