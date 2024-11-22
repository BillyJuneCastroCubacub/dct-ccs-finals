<?php
session_start();
$pageTitle = "Delete Student Record";
require_once('../../functions.php');
include('../partials/header.php');

// Initialize variables
$errors = [];
$studentDetails = null;

// Check if a student ID is provided
if (!empty($_GET['student_id'])) {
    $student_id = sanitize_input($_GET['student_id']);
    $studentDetails = getSelectedStudentById($student_id);

    if (!$studentDetails) {
        $errors[] = "Student record not found.";
    }
}

// Handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['student_id'])) {
    $student_id = sanitize_input($_POST['student_id']);

    if (deleteStudentById($student_id)) {
        // Redirect to student registration page after successful deletion
        header("Location: register.php");
        exit;
    } else {
        $errors[] = "Unable to delete the student record. Please try again.";
    }
}
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Delete Student Record</h2>
            <nav class="breadcrumb mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                    <li class="breadcrumb-item active">Delete Student</li>
                </ol>
            </nav>
            <div class="card mt-4">
                <div class="card-body">

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

                    <!-- Show Student Details -->
                    <?php if ($studentDetails): ?>
                        <h5>Are you sure you want to delete the following student?</h5>
                        <ul>
                            <li><strong>Student ID:</strong> <?= htmlspecialchars($studentDetails['student_id']); ?></li>
                            <li><strong>First Name:</strong> <?= htmlspecialchars($studentDetails['first_name']); ?></li>
                            <li><strong>Last Name:</strong> <?= htmlspecialchars($studentDetails['last_name']); ?></li>
                        </ul>
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentDetails['student_id']); ?>">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='register.php';">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Deletion</button>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">No student record found to delete.</p>
                        <a href="register.php" class="btn btn-primary">Return to Student List</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
