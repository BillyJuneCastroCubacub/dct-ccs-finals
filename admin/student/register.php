<?php
session_start();
require_once('../../functions.php');
include('../partials/header.php');


$errors = [];
$student_data = [];


if (!isset($_SESSION['student_data'])) {
    $_SESSION['student_data'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $student_data = [
        'student_id' => trim($_POST['student_id']),
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
    ];

    
    $errors = validateStudentData($student_data);

    if (empty($errors)) {
        $duplicate_errors = checkDuplicateStudentData($student_data['student_id']);

        if (!empty($duplicate_errors)) {
            $errors = array_merge($errors, $duplicate_errors);
        } else {
            $isAdded = addStudentData(
                $student_data['student_id'],
                $student_data['first_name'],
                $student_data['last_name']
            );

            if ($isAdded) {
                header("Location: register.php");
                exit;
            } else {
                $errors[] = "Error: Could not add the student. Please try again.";
            }
        }
    }
}
?>

<!-- HTML Section -->
<div class="container-fluid">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Register a New Student</h2>
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Register Student</li>
                </ol>
            </nav>
            <hr>

            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" 
                           placeholder="Enter Student ID" 
                           value="<?php echo isset($student_data['student_id']) ? htmlspecialchars($student_data['student_id']) : ''; ?>">
                </div>
                <div class="form-group mt-3">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           placeholder="Enter First Name" 
                           value="<?php echo isset($student_data['first_name']) ? htmlspecialchars($student_data['first_name']) : ''; ?>">
                </div>
                <div class="form-group mt-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           placeholder="Enter Last Name" 
                           value="<?php echo isset($student_data['last_name']) ? htmlspecialchars($student_data['last_name']) : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary mt-3" style="width:100%;">Add Student</button>
            </form>
            <hr>

            <!-- Student List -->
            <h3 class="mt-5">Student List</h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $students = selectStudents();
                        if(!empty($students)):?>
                            <?php foreach($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm">Edit</a>
                                        <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                        <a href="#" class="btn btn-warning btn-sm">Attach Subject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No student records found.</td>
                    </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
