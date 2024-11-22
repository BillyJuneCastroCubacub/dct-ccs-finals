
<?php
session_start();
$pageTitle = "Edit Student";
require_once('../../functions.php');
include('../partials/header.php');

$errors = [];
$studentToEdit = null;

// Check if a student ID is provided
if (isset($_GET['student_id'])) {
    $student_id = sanitize_input($_GET['student_id']);
    $studentToEdit = getSelectedStudentById($student_id);

    if (!$studentToEdit) {
        $errors[] = "The specified student could not be found.";
    }
}

// Process form submission for updating student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = sanitize_input($_POST['student_id']); // Readonly field, must match original
    $updatedData = [
        'student_id' => $student_id,
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name'])
    ];

    // Validate the updated data
    $validationErrors = validateStudentData($updatedData);
    $errors = array_merge($errors, $validationErrors);

    // If no validation errors, attempt to update
    if (empty($errors)) {
        $isUpdated = updateStudentData($updatedData);

        if ($isUpdated) {
            header("Location: register.php");
            exit;
        } else {
            $errors[] = "There was an issue updating the student's details. Please try again.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Update Student Information</h2>
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Student Registration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                </ol>
            </nav>
            <hr>

            <!-- Display errors if any -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <form method="post">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="student_id" 
                        name="student_id" 
                        value="<?= htmlspecialchars($studentToEdit['student_id'] ?? '') ?>" 
                        readonly>
                </div>
                <div class="form-group mt-3">
                    <label for="first_name">First Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="first_name" 
                        name="first_name" 
                        value="<?= htmlspecialchars($studentToEdit['first_name'] ?? '') ?>" 
                        required>
                </div>
                <div class="form-group mt-3">
                    <label for="last_name">Last Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="last_name" 
                        name="last_name" 
                        value="<?= htmlspecialchars($studentToEdit['last_name'] ?? '') ?>" 
                        required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>
    </div>
</div>
