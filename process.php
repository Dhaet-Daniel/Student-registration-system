<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function sendJsonResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function getRequestData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $rawInput = file_get_contents('php://input');
        $decoded = json_decode($rawInput, true);
        return is_array($decoded) ? $decoded : [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return $_POST;
    }

    parse_str(file_get_contents('php://input'), $parsedInput);
    return is_array($parsedInput) ? $parsedInput : [];
}

function normalizeStudentData(array $input): array
{
    return [
        'id' => isset($input['id']) ? (int) $input['id'] : 0,
        'student_id' => trim((string) ($input['studentID'] ?? $input['student_id'] ?? '')),
        'full_name' => trim((string) ($input['studentName'] ?? $input['full_name'] ?? '')),
        'email' => trim((string) ($input['email'] ?? '')),
        'program' => trim((string) ($input['program'] ?? '')),
    ];
}

function validateStudentData(array $student): ?string
{
    if ($student['student_id'] === '' || $student['full_name'] === '' || $student['email'] === '' || $student['program'] === '') {
        return 'All fields are required.';
    }

    if (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }

    return null;
}

function fetchStudents(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, student_id, full_name, email, program, registration_date FROM Students ORDER BY registration_date DESC, id DESC');
    return $stmt->fetchAll();
}

function fetchDashboardStats(PDO $pdo): array
{
    $summary = $pdo->query(
        'SELECT
            COUNT(*) AS total_students,
            COUNT(DISTINCT program) AS total_programs,
            MAX(registration_date) AS latest_registration
         FROM Students'
    )->fetch();

    $recentCountStmt = $pdo->prepare('SELECT COUNT(*) FROM Students WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
    $recentCountStmt->execute();

    $programBreakdown = $pdo->query(
        'SELECT program, COUNT(*) AS total
         FROM Students
         GROUP BY program
         ORDER BY total DESC, program ASC'
    )->fetchAll();

    return [
        'totalStudents' => (int) ($summary['total_students'] ?? 0),
        'totalPrograms' => (int) ($summary['total_programs'] ?? 0),
        'recentRegistrations' => (int) $recentCountStmt->fetchColumn(),
        'latestRegistration' => $summary['latest_registration'] ?? null,
        'programBreakdown' => $programBreakdown,
    ];
}

try {
    initializeDatabase();
    $pdo = createDatabaseConnection();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $input = getRequestData();

    if ($method === 'GET') {
        requireAdminAccess();
        sendJsonResponse(200, [
            'success' => true,
            'data' => fetchStudents($pdo),
            'stats' => fetchDashboardStats($pdo),
        ]);
    }

    if ($method === 'POST') {
        $student = normalizeStudentData($input);
        $validationError = validateStudentData($student);

        if ($validationError !== null) {
            sendJsonResponse(400, ['success' => false, 'message' => $validationError]);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO Students (student_id, full_name, email, program)
             VALUES (:student_id, :full_name, :email, :program)'
        );
        $stmt->execute([
            ':student_id' => $student['student_id'],
            ':full_name' => $student['full_name'],
            ':email' => $student['email'],
            ':program' => $student['program'],
        ]);

        sendJsonResponse(201, [
            'success' => true,
            'message' => 'Student registered successfully.',
            'data' => fetchStudents($pdo),
            'stats' => fetchDashboardStats($pdo),
        ]);
    }

    if ($method === 'PUT') {
        requireAdminAccess();
        $student = normalizeStudentData($input);
        $validationError = validateStudentData($student);

        if ($student['id'] <= 0) {
            sendJsonResponse(400, ['success' => false, 'message' => 'A valid student record is required for editing.']);
        }

        if ($validationError !== null) {
            sendJsonResponse(400, ['success' => false, 'message' => $validationError]);
        }

        $stmt = $pdo->prepare(
            'UPDATE Students
             SET student_id = :student_id,
                 full_name = :full_name,
                 email = :email,
                 program = :program
             WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $student['id'],
            ':student_id' => $student['student_id'],
            ':full_name' => $student['full_name'],
            ':email' => $student['email'],
            ':program' => $student['program'],
        ]);

        if ($stmt->rowCount() === 0) {
            sendJsonResponse(404, ['success' => false, 'message' => 'Student not found or no changes were made.']);
        }

        sendJsonResponse(200, [
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => fetchStudents($pdo),
            'stats' => fetchDashboardStats($pdo),
        ]);
    }

    if ($method === 'DELETE') {
        requireAdminAccess();
        $id = isset($input['id']) ? (int) $input['id'] : 0;

        if ($id <= 0) {
            sendJsonResponse(400, ['success' => false, 'message' => 'A valid student record is required for deletion.']);
        }

        $stmt = $pdo->prepare('DELETE FROM Students WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            sendJsonResponse(404, ['success' => false, 'message' => 'Student not found.']);
        }

        sendJsonResponse(200, [
            'success' => true,
            'message' => 'Student deleted successfully.',
            'data' => fetchStudents($pdo),
            'stats' => fetchDashboardStats($pdo),
        ]);
    }

    sendJsonResponse(405, ['success' => false, 'message' => 'Method not allowed.']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $message = str_contains($e->getMessage(), 'student_id')
            ? 'That student ID is already registered.'
            : 'That email address is already registered.';

        sendJsonResponse(409, ['success' => false, 'message' => $message]);
    }

    sendJsonResponse(500, [
        'success' => false,
        'message' => 'Database connection failed. Check that MySQL is running and the credentials in db.php are correct.',
    ]);
}
