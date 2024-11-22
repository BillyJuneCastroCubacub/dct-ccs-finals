
<?php 
    session_start();
    $pageTitle = "Edit Student";
    include('../../functions.php');
    include('../partials/header.php'); 
?>
<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Edit Student</h2>
            <br>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                </ol>
            </nav>
            <hr>
            <br>
            <form method="post">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" readonly>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name">
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </form>
        </div>
    </div>
</div>