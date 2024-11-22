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

    function deleteStudentById($student_id) {
        $conn = db_connect(); 
    
        $sql = "DELETE FROM students WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $student_id);
            $executionResult = mysqli_stmt_execute($stmt);
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
    
            return $executionResult; 
        } else {
            handleDatabaseError("Error preparing delete query", $conn);
        }
        mysqli_close($conn);
        return false; 
    }


    function assignSubjectToStudent($studentId, $subjectId) {
        $conn = db_connect();
        $errors = [];
    
        // Input validation
        if (!$studentId) {
            $errors[] = "Student ID is required.";
        }
        if (!$subjectId) {
            $errors[] = "Subject ID is required.";
        }
    
        // Check if student and subject exist
        $student = getSelectedStudentById($studentId);
        $subject = getSelectedSubjectById($subjectId);
    
        if (!$student) {
            $errors[] = "No student found with the provided ID.";
        }
        if (!$subject) {
            $errors[] = "No subject found with the provided ID.";
        }
    
        // Handle validation errors
        if (!empty($errors)) {
            echo displayErrors($errors);
            return false;
        }
    
        // Check if the subject is already assigned to the student
        $queryCheck = "SELECT * FROM students_subjects WHERE student_id = ? AND subject_id = ?";
        $stmtCheck = mysqli_prepare($conn, $queryCheck);
    
        if ($stmtCheck) {
            mysqli_stmt_bind_param($stmtCheck, "ii", $studentId, $subjectId);
            mysqli_stmt_execute($stmtCheck);
            mysqli_stmt_store_result($stmtCheck);
    
            if (mysqli_stmt_num_rows($stmtCheck) > 0) {
                mysqli_stmt_close($stmtCheck);
                mysqli_close($conn);
                echo displayErrors(["This subject is already assigned to the student."]);
                return false;
            }
            mysqli_stmt_close($stmtCheck);
        } else {
            echo "Error checking existing assignments: " . mysqli_error($conn);
            mysqli_close($conn);
            return false;
        }
    
        // Assign the subject to the student
        $queryInsert = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $queryInsert);
    
        if ($stmtInsert) {
            $defaultGrade = 0; // Set default grade value
            mysqli_stmt_bind_param($stmtInsert, "iis", $studentId, $subjectId, $defaultGrade);
    
            if (mysqli_stmt_execute($stmtInsert)) {
                mysqli_stmt_close($stmtInsert);
                mysqli_close($conn);
                return true; // Assignment successful
            } else {
                echo "Error assigning subject: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmtInsert);
        } else {
            echo "Error preparing assignment query: " . mysqli_error($conn);
        }
    
        // Close the connection in case of failure
        mysqli_close($conn);
        return false;
    }


    function fetchSubjectIdByCode($subjectCode) {
        $conn = db_connect(); // Establish database connection
    
        // Prepare the SQL query to fetch subject ID using the subject code
        $query = "SELECT id FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $query);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subjectCode);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            // Fetch and return the subject ID if found
            if ($result && $row = mysqli_fetch_assoc($result)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $row['id'];
            }
    
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return false; // Return false if not found or if an error occurs
    }

    function fetchSubjectDetailsByCode($subjectCode) {
        $conn = db_connect(); // Establish database connection
        $subject = null;
    
        // Prepare the SQL query to fetch all subject details using the subject code
        $query = "SELECT * FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $query);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subjectCode);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            // Fetch and store subject details if found
            if ($result) {
                $subject = mysqli_fetch_assoc($result);
            }
    
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return $subject; // Return the subject details or null if not found
    }
    
    function getAttachedSubjectsByStudentId($student_id) {
        $conn = db_connect();
        $subjects = [];
        
      
        $sql = "SELECT s.subject_code, s.subject_name, ss.grade
                FROM subjects s
                INNER JOIN students_subjects ss ON s.id = ss.subject_id
                WHERE ss.student_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $subjects[] = $row;
            }
    
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return $subjects;
    }

    function fetchAllSubjects() {
        $conn = db_connect(); // Establish a database connection
        $subjects = [];
        
        // Query to fetch all subjects
        $sql = "SELECT * FROM subjects";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $subjects[] = $row;
            }
        } else {
            // Handle query errors
            echo "Error fetching subjects: " . mysqli_error($conn);
        }
        
        mysqli_close($conn);
        return $subjects;
    }
    
    function getSelectedSubjectById($subject_id) {
        $conn = db_connect(); // Establish a database connection
    
        // SQL query to fetch the subject details by ID
        $sql = "SELECT * FROM subjects WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $subject_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            if ($result && mysqli_num_rows($result) > 0) {
                $subject = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $subject; // Return the subject details
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return null; // No subject found
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    
        mysqli_close($conn);
        return null; // Return null in case of an error
    }


    function detachSubjectFromStudent($student_id, $subject_id) {
        $conn = db_connect();
        $sql = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $subject_id);
            mysqli_stmt_execute($stmt);
    
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true; 
            } else {
                error_log("No rows affected. Query might have failed.");
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return false; 
            }
        } else {
            error_log("Failed to prepare statement: " . mysqli_error($conn));
            mysqli_close($conn);
            return false; 
        }
    }
    
    
    
    
    

?>
