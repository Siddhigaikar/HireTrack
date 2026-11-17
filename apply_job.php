
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

$sql = $conn->prepare("select resume from candidate_profile where user_id=?");
$sql->bind_param('i',$candidate_id);
$sql->execute();
$sql->bind_result($resume);
$sql->fetch();
$sql->close();

if($resume == ""){
    die("Please upload your resume in your profile before applying.");
}

$check = $conn->prepare("select id from applications where job_id=? and candidate_id=?");
$check->bind_param('ii',$job_id,$candidate_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    echo "You have already applied for this job.";
}
else{

    $sql = $conn->prepare("insert into applications (job_id,candidate_id,resume) values (?,?,?)");
    $sql->bind_param('iis',$job_id,$candidate_id,$resume);

    if($sql->execute()){
        echo "Application Submitted Successfully";
    }
    else{
        echo "Application Failed";
    }
}

$check->close();
?>

<!doctype html>
<html lang="en">

<head>
    <title>HireTrack - Apply Job</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container col-md-6 mt-5 shadow rounded p-4 bg-white text-center">

        <h2 class="mb-4">Job Application</h2>

        <p>Your application process has been completed.</p>

        <a href="view_jobs.php" class="btn btn-primary">Back to Jobs</a>

        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>

    </div>

</body>
</html>
