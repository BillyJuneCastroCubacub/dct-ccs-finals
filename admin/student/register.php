<?php
    session_start();
    require('../../functions.php');
    include('../partials/header.php');

    $errors = [];
    $student_data = [];

    if (!isset($_SESSION['student_data'])) {
        $_SESSION['student_data'] = [];
    }

    $student_data = [
        'student_id' => $_POST['student_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
    ];

    $errors = validateData($student_data);

  

?>

<div class="container-fluid">
    <div class="row">
            <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Register a New Student</h2>
            <br>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Register Student</li>
                </ol>
            </nav>
            <hr>

            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID">
                </div>
                <div class="form-group mt-3">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name">
                </div>
                <div class="form-group mt-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Add Student</button>
            </form>
            <hr>

          
            <h3 class="mt-5">Student List</h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm">Delete</a>
                            <a href="#" class="btn btn-secondary btn-sm">Attach Subject</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center">No student records found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../partials/footer.php');