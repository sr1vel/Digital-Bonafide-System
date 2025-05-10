<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection details should ideally be stored in a separate, secure configuration file
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hackathon";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from all tables
    $stmt = $pdo->prepare("
        SELECT 'PG' as source, course, year, reason FROM pganalysis
        UNION ALL
        SELECT 'UG' as source, course, year, reason FROM uganalysis
        UNION ALL
        SELECT 'Passed' as source, course, year, reason FROM passedanalysis
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the header row
$sheet->setCellValue('A1', 'Source');
$sheet->setCellValue('B1', 'Course');
$sheet->setCellValue('C1', 'Year');
$sheet->setCellValue('D1', 'Reason');

// Fill data
$row = 2;
foreach ($data as $record) {
    $sheet->setCellValue('A' . $row, $record['source']);
    $sheet->setCellValue('B' . $row, $record['course']);
    $sheet->setCellValue('C' . $row, $record['year']);
    $sheet->setCellValue('D' . $row, $record['reason']);
    $row++;
}

$writer = new Xlsx($spreadsheet);

// Generate and send the Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="student_analysis.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
