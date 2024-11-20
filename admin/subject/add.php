<?php 

    session_start();
    require('../../functions.php');
    include('../partials/header.php');

    $errors = [];
    $subject_data = [];

if (!isset($_SESSION['subject_data'])) {
    $_SESSION['subject_data'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_data = [
        'subject_code' => $_POST['subject_code'],
        'subject_name' => $_POST['subject_name']
    ];

    $errors = validateSubjectData($subject_data);

    
    foreach ($_SESSION['subject_data'] as $existingSubject) {
        if ($existingSubject['subject_code'] === $subject_data['subject_code']) {
            $errors[] = "Duplicate Subject";
            break;
        }
        if ($existingSubject['subject_name'] === $subject_data['subject_name']) {
            $errors[] = "Duplicate Subject";
            break;
        }
    }

    if (empty($errors)) {
        $_SESSION['subject_data'][] = $subject_data;
        header("Location: add.php");
        exit;
    }
}

?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Section -->
        <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9">
            <div class="container mt-5">
                <h2>Add a New Subject</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                    </ol>
                </nav>
                <hr>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>System Errors</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code">
                    </div>
                    <div class="form-group mt-3">
                        <label for="subject_name">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Subject</button>
                </form>
                <hr>

                <!-- Subject List -->
                <h3 class="mt-5">Subject List</h3>
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>
