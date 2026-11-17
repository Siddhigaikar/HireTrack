
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

$sql = $conn->prepare("select image from jobs where id=? and employer_id=?");
$sql->bind_param('ii',$job_id,$employer_id);
$sql->execute();
$result = $sql->get_result();
$job = $result->fetch_assoc();

if($job){

    if($job['image'] != "" && file_exists("uploads/".$job['image'])){
        unlink("uploads/".$job['image']);
    }

    $delete = $conn->prepare("delete from jobs where id=? and employer_id=?");
    $delete->bind_param('ii',$job_id,$employer_id);

    $delete->execute();
}

header("Location:dashboard.php");
exit();
?>
