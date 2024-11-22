<?php
session_start();
$pageTitle = "Detach Subject from Student";
include '../partials/header.php';
include '../../functions.php';
?>
<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Detach Subject from Student</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                    <?php if (isset($studentToDelete)): ?>
                        <li class="breadcrumb-item">
                            <a href="attach-subject.php?student_id=<?= htmlspecialchars($studentToDelete['student_id']); ?>">Attach Subject to Student</a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page">Detach Subject from Student</li>
                </ol>
            </nav>
            <div class="card mt-3">
                <div class="card-body">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error_message']); ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>
                    
                    <?php if ($studentToDelete && $subjectToDetach): ?>
                        <h5>Are you sure you want to detach this subject from this student record?</h5>
                        <ul>
                            <li><strong>Student ID:</strong> <?= htmlspecialchars($studentToDelete['student_id']) ?></li>
                            <li><strong>First Name:</strong> <? htmlspecialchars($studentToDelete['first_name']) ?></li>
                            <li><strong>Last Name:</strong> <?= htmlspecialchars($studentToDelete['last_name']) ?></li>
                            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subjectToDetach['subject_code']) ?></li>
                            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subjectToDetach['subject_name']) ?></li>
                        </ul>
                        <form method="POST">
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentToDelete['student_id']) ?>">
                            <input type="hidden" name="subject_code" value="<?= htmlspecialchars($subjectToDetach['subject_code']) ?>">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='attach-subject.php?student_id=<?= htmlspecialchars($studentToDelete['student_id']); ?>'">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">Detach Subject from Student</button>
                        </form>

                    <?php else: ?>
                        <p class="text-danger">Student or subject not found.</p>
                        <a href="attach-subject.php" class="btn btn-primary">Back to Student List</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>