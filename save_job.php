
<?php
include "db.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != "candidate"){
    header("Location:login.php");
    exit();
}

$candidate_id = $_SESSION['id'];
$job_id = $_GET['id'];

$check = $conn->prepare("select id from jobs where id=? and status='Active'");
$check->bind_param('i',$job_id);
$check->execute();
$check->store_result();

if($check->num_rows == 0){
    die("Job Not Found");
}

$check->close();

$check = $conn->prepare("select id from saved_jobs where candidate_id=? and job_id=?");
$check->bind_param('ii',$candidate_id,$job_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    echo "Job Already Saved";
}
else{

    $sql = $conn->prepare("insert into saved_jobs (candidate_id,job_id) values (?,?)");
    $sql->bind_param('ii',$candidate_id,$job_id);

    if($sql->execute()){
        echo "Job Saved Successfully";
    }
    else{
        echo "Job Not Saved";
    }
}

$check->close();
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - Save Job</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container col-md-5 mt-5 shadow rounded p-4 bg-white text-center">

        <h2 class="mb-4">Save Job</h2>

        <p>Check your saved jobs whenever you want.</p>

        <a href="view_jobs.php" class="btn btn-primary">Browse Jobs</a>

        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>

    </div>

</body>
</html>
