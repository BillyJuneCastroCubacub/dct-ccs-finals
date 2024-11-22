<?php
session_start();
$pageTitle = "Add New Subject";
require_once('../../functions.php');
include('../partials/header.php');

// Initialize error messages
$errorMessages = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSubject = [
        'subject_code' => sanitize_input($_POST['subject_code']),
        'subject_name' => sanitize_input($_POST['subject_name']),
    ];

    // Add subject to database
    $addResult = addSubjectData($newSubject);

    if ($addResult === true) {
        // Redirect to avoid form resubmission
        header("Location: add.php");
        exit;
    } else {
        $errorMessages = $addResult;
    }
}

// Fetch all subjects from the database
$conn = db_connect();
$sql = "SELECT * FROM subjects";
$result = mysqli_query($conn, $sql);
$allSubjects = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Create a Subject</h2>
            <nav aria-label="breadcrumb" class="my-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subjects</li>
                </ol>
            </nav>
            <hr>

            <!-- Error Notifications -->
            <?php if (!empty($errorMessages)): ?>
                <div class="alert alert-danger">
                    <strong>Errors Found:</strong>
                    <ul>
                        <?php foreach ($errorMessages as $message): ?>
                            <li><?= htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Subject Form -->
            <form method="post" class="mt-4">
                <div class="form-group mb-3">
                    <label for="subject_code">Subject Code</label>
                    <input 
                        type="text" 
                        id="subject_code" 
                        name="subject_code" 
                        class="form-control" 
                        placeholder="Enter subject code" 
                        value="<?= isset($newSubject['subject_code']) ? htmlspecialchars($newSubject['subject_code']) : ''; ?>" 
                        required>
                </div>
                <div class="form-group mb-3">
                    <label for="subject_name">Subject Name</label>
                    <input 
                        type="text" 
                        id="subject_name" 
                        name="subject_name" 
                        class="form-control" 
                        placeholder="Enter subject name" 
                        value="<?= isset($newSubject['subject_name']) ? htmlspecialchars($newSubject['subject_name']) : ''; ?>" 
                        required>
                </div>
                <button type="submit" class="btn btn-success">Add Subject</button>
            </form>

            <hr>
            <h3 class="mt-5">Available Subjects</h3>
            <table class="table table-bordered mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allSubjects)): ?>
                        <?php foreach ($allSubjects as $subject): ?>
                            <tr>
                                <td><?= htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <a href="edit.php?subject_code=<?= urlencode($subject['subject_code']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete.php?subject_code=<?= urlencode($subject['subject_code']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No subjects have been added yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
