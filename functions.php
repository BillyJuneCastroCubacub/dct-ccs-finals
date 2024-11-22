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

function getSelectedStudentById($student_id) {
    $conn = db_connect();

    try {
        $sql = "SELECT * FROM students WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $student_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $student = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $student; 
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return null; 
            }
        }
    } catch (Exception $e) {
    }
}

function executeQuery($sql, $params, $isSelect = false) {
    $conn = db_connect();
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
        mysqli_stmt_execute($stmt);

        if ($isSelect) {
            $result = mysqli_stmt_get_result($stmt);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $data;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return true;
    }
}
function fetchStudentById($student_id) {
        $result = executeQuery("SELECT * FROM students WHERE student_id = ?", [$student_id], true);
        return $result[0] ?? null;
    }
    
    function updateStudentData($studentData) {
        $sql = "UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?";
        return executeQuery($sql, [$studentData['first_name'], $studentData['last_name'], $studentData['student_id']]);
    }

    function deleteSubjectByCode($subject_code) {
        $conn = db_connect(); 
        $sql = "DELETE FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            $executionResult = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $executionResult;
        } else {
         
            echo "Error preparing delete query: " . mysqli_error($conn);
            mysqli_close($conn);
            return false;
        }
    }

    function addSubjectData($subject_data) {
        $conn = db_connect(); 
        
        $errors = validateSubjectData($subject_data);
        
        $sql_check = "SELECT * FROM subjects WHERE subject_code = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $subject_data['subject_code']);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
    
        if (mysqli_num_rows($result_check) > 0) {
            $errors[] = "Duplicate Subject";
        }
        mysqli_stmt_close($stmt_check);
    
    
        $sql_check_name = "SELECT * FROM subjects WHERE subject_name = ?";
        $stmt_check_name = mysqli_prepare($conn, $sql_check_name);
        mysqli_stmt_bind_param($stmt_check_name, "s", $subject_data['subject_name']);
        mysqli_stmt_execute($stmt_check_name);
        $result_check_name = mysqli_stmt_get_result($stmt_check_name);
    
        if (mysqli_num_rows($result_check_name) > 0) {
            $errors[] = "Duplicate Subject";
        }
        mysqli_stmt_close($stmt_check_name);
    
       
        if (empty($errors)) {
            $sql = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $subject_data['subject_code'], $subject_data['subject_name']);
                $execute = mysqli_stmt_execute($stmt);
    
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
    
                return $execute ? true : ["Error adding subject to the database."];
            } else {
                $errors[] = "Error preparing statement: " . mysqli_error($conn);
            }
        }
    
        mysqli_close($conn);
        return $errors;
    }

    function checkDuplicateSubjectData($subject_code) {
        $conn = db_connect();
        $query = "SELECT subject_code FROM subjects WHERE subject_code = ?";
        $errors = [];
    
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_fetch_assoc($result)) {
                $errors[] = "Duplicate Subject";
            }
    
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Error checking duplicate subject code";
        }
    
        mysqli_close($conn);
        return $errors;
    }
    


    function getSubjectByCode($subject_code) {
        $conn = db_connect();
        $query = "SELECT * FROM subjects WHERE subject_code = ?";
        $subject = null;
    
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
            $subject = mysqli_fetch_assoc($result);
    
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return $subject;
    }
    

?>
