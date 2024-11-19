<?php
include 'db_connect.php';

// Enable MySQLi to throw exceptions for errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    try {
        // Prepare and execute the delete statement
        $query = "DELETE FROM books WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $bookId);
        
        if (!$stmt->execute()) {
            // If execution failed, manually trigger the error handler
            throw new Exception("Failed to delete book due to foreign key constraint.");
        }

        // Check if any rows were affected by the DELETE query
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            // If no rows were affected, it likely means the book couldn't be deleted due to constraints
            echo json_encode(['status' => 'error', 'message' => 'Book not deleted. Check foreign key constraints or if it exists.']);
        }
    } catch (mysqli_sql_exception $e) {
        // Specifically handle foreign key constraint error (1451)
        if ($e->getCode() == 1451) {
            echo json_encode(['status' => 'error', 'message' => 'This book cannot be deleted as it is referenced in other records.']);
        } else {
            // Other SQL errors
            echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        // General catch for any other exceptions
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
