<?php
session_start();
$pageTitle = "Remove Subject";
require_once('../../functions.php');
include('../partials/header.php');



$selectedSubject = null;
$errorMessages = [];


if (isset($_GET['subject_code'])) {
    $subjectCode = sanitize_input($_GET['subject_code']);
    $selectedSubject = getSubjectByCode($subjectCode);

    if (!$selectedSubject) {
        $errorMessages[] = "The subject does not exist.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_code'])) {
    $subjectCode = sanitize_input($_POST['subject_code']);

    if (deleteSubjectByCode($subjectCode)) {
        header("Location: add.php");
        exit;
    } else {
        $errorMessages[] = "An error occurred while attempting to delete the subject. Please try again.";
    }
}

?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Remove Subject</h2>
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="add.php">Manage Subjects</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Remove Subject</li>
                </ol>
            </nav>
            <div class="card mt-4">
                <div class="card-body">
                    <!-- Display any error messages -->
                    <?php if (!empty($errorMessages)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errorMessages as $message): ?>
                                    <li><?= htmlspecialchars($message); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                 
                    <?php if ($selectedSubject): ?>
                        <h5>Are you sure you want to delete this subject?</h5>
                        <ul>
                            <li><strong>Code:</strong> <?= htmlspecialchars($selectedSubject['subject_code']); ?></li>
                            <li><strong>Name:</strong> <?= htmlspecialchars($selectedSubject['subject_name']); ?></li>
                        </ul>
                        <form method="post">
                            <input type="hidden" name="subject_code" value="<?= htmlspecialchars($selectedSubject['subject_code']); ?>">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='add.php';">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
