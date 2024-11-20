<?php

// Database connection function
function db_connect() {
    $servername = "localhost";
    $username = "root"; // Replace with your database username
    $password = ""; // Replace with your database password
    $dbname = "dct-ccs-finals"; // Replace with your database name

    // Create and return the database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to validate user login
function login_user($email, $password) {
    $conn = db_connect();
    $hashed_password = md5($password); // Hashing the password for security

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user; // Return user details on success
    } else {
        $stmt->close();
        $conn->close();
        return false; // Login failed
    }
}

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

function validateSubjectData($subject_data) {
    $errors = [];
    if (empty($subject_data['subject_code'])) {
        $errors[] = "Subject Code is required.";
    }
    if (empty($subject_data['subject_name'])) {
        $errors[] = "Subject Name is required.";
    }
    return $errors;
}

function validateStudentData($student_data) {
    $errors = [];
    if (empty($student_data['student_id'])) {
        $errors[] = "Student ID is required.";
    }
    if (empty($student_data['first_name'])) {
        $errors[] = "First Name is required.";
    }

    if (empty($student_data['last_name'])) {
        $errors[] = "Last Name is required.";
    }
    return $errors;
}


?>
