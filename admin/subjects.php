<?php include('partials/header.php'); ?>

<div class="container mt-5">
    <h2>Add a New Subject</h2>
    <br>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
        </ol>
    </nav>
    <hr>
    <form method="post">
        <div class="form-group">
            <label for="subject_code">Subject Code</label>
            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code">
        </div>
        <div class="form-group">
            <label for="subject_name">Subject Name</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name">
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Add Subject</button>
    </form>
    <hr>
    <h3 class="mt-5">Subject List</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Options</th>
            </tr>
        </thead>
        <tbody>

                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <a href="#">Edit</a>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                <tr>
                    <td colspan="3" class="text-center">No subjects found.</td>
                </tr>
        </tbody>
    </table>
</div>


<?php include('partials/footer.php'); ?>