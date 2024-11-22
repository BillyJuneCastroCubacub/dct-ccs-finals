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
function checkDuplicateStudentData($student_id) {
    $conn = db_connect(); 

    $sql = "SELECT student_id FROM students WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
       
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existing_student = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        
        if ($existing_student) {
            return ["Duplicate Student ID" . $existing_student];
        }
    } else {
        mysqli_close($conn);
        return ["Error checking duplicate student ID"];
    }

  
    return [];
}

function addStudentData($student_id, $student_firstname, $student_lastname) {
    $checkStudentData = validateStudentData([
        'student_id' => $student_id,
        'first_name' => $student_firstname,
        'last_name' => $student_lastname,
    ]);
    $checkDuplicateData = checkDuplicateStudentData($student_id);

    if (count($checkStudentData) > 0) {
        echo displayErrors($checkStudentData);
        return false;
    }

    if (count($checkDuplicateData) > 0) {
        echo displayErrors($checkDuplicateData);
        return false;
    }

    $conn = db_connect();


    $sql_insert = "INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert);

    if ($stmt) {
        
        mysqli_stmt_bind_param($stmt, "sss", $student_id, $student_firstname, $student_lastname);

     
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return true;
        } else {
            echo "Error: " . mysqli_error($conn); 
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }

   
    mysqli_close($conn);
    return false;
}
function selectStudents() {
    $conn = db_connect();

    $sql_select = "SELECT * FROM students";
    $result = mysqli_query($conn, $sql_select);

    $students = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    mysqli_close($conn);

    return $students;
}

?>
