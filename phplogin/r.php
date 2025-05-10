
<?php
// Database connection details should ideally be stored in a separate, secure configuration file
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "hackathon"; // Contains variables $db_host, $db_user, $db_pass, $db_name

try {
    // Create connection with PDO to use prepared statements
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data and aggregate by course
    $stmtCourse = $pdo->prepare("
        SELECT course, COUNT(*) as count FROM (
            SELECT course FROM pganalysis
            UNION ALL
            SELECT course FROM uganalysis
            UNION ALL
            SELECT course FROM passedanalysis
        ) as combined
        GROUP BY course
    ");
    $stmtCourse->execute();
    $courseData = $stmtCourse->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data and aggregate by year
    $stmtYear = $pdo->prepare("
        SELECT year, COUNT(*) as count FROM (
            SELECT year FROM pganalysis
            UNION ALL
            SELECT year FROM uganalysis
        ) as combined
        GROUP BY year
    ");
    $stmtYear->execute();
    $yearData = $stmtYear->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data and aggregate by reason
    $stmtReason = $pdo->prepare("
        SELECT reason, COUNT(*) as count FROM (
            SELECT reason FROM pganalysis
            UNION ALL
            SELECT reason FROM uganalysis
            UNION ALL
            SELECT reason FROM passedanalysis
        ) as combined
        GROUP BY reason
    ");
    $stmtReason->execute();
    $reasonData = $stmtReason->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all data from pganalysis
    $stmtPg = $pdo->prepare("SELECT course, year, reason, Date, rollnumber FROM approvecurrent");
    $stmtPg->execute();
    $pgData = $stmtPg->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all data from uganalysis

    // Fetch all data from passedanalysis
    $stmtPassed = $pdo->prepare("SELECT course, reason, rollnumber, Date FROM rejection");
    $stmtPassed->execute();
    $passedData = $stmtPassed->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total counts
    $totalCourses = array_sum(array_column($courseData, 'count'));
    $totalYears = array_sum(array_column($yearData, 'count'));
    $totalReasons = array_sum(array_column($reasonData, 'count'));
    $stmtTotalPg = $pdo->prepare("SELECT COUNT(*) as count FROM pganalysis");
    $stmtTotalPg->execute();
    $totalPg = $stmtTotalPg->fetch(PDO::FETCH_ASSOC)['count'];

    $stmtTotalUg = $pdo->prepare("SELECT COUNT(*) as count FROM uganalysis");
    $stmtTotalUg->execute();
    $totalUg = $stmtTotalUg->fetch(PDO::FETCH_ASSOC)['count'];

    $stmtTotalPassed = $pdo->prepare("SELECT COUNT(*) as count FROM passedanalysis");
    $stmtTotalPassed->execute();
    $totalPassed = $stmtTotalPassed->fetch(PDO::FETCH_ASSOC)['count'];

    $totalStudentsApplied = $totalPg + $totalUg + $totalPassed;

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.5.0/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            width: 100%;
            margin-bottom: 20px;
        }

        .chart-item {
            width: 30%;
            margin-bottom: 20px;
        }

        .chart-container canvas {
            width: 100% !important;
            height: auto !important;
            max-height: 300px;
        }

        .totals {
            margin: 20px;
            font-size: 18px;
        }

        .navbar {
            overflow: visible;
            /* Changed from hidden to visible */
            background-color: #092a47;
            padding: 15px;
            position: relative;
            z-index: 1;
        }

        .navbar a {
            float: right;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ffff;
            color: black;
        }

        .logout {
            float: right;
        }

        .table-container {
            width: 80%;
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            float :none;
            margin-left: 670px;
        }

        .dropbtn {
            background-color: #092a47;
            color: white;
            padding: 14px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 2;
            top: 100%;
            left: 0;
            flex-direction: column;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a class="logout" href="#">Logout</a>
        <a href="statistics1.php">Home</a>


        <div id="exportDropdown" class="dropdown">
            <a href="#" id="exportBtn">Export</a>
            <div id="dropdownContent" class="dropdown-content">
                <a href="#" data-format="csv">CSV</a>
                <a href="#" data-format="pdf">PDF</a>
                <a href="#" data-format="excel">Excel</a>
            </div>
        </div>
    </div>
    <h1>Analysis Statistics</h1>

    <div class="chart-container">
        <!-- Pie Chart for Course Analysis -->
        <div class="chart-item">
            <h2>Course Analysis</h2>
            <canvas id="coursePieChart"></canvas>
        </div>

        <!-- Pie Chart for Year Analysis -->
        <div class="chart-item">
            <h2>Year Analysis</h2>
            <canvas id="yearPieChart"></canvas>
        </div>

        <!-- Pie Chart for Reason Analysis -->
        <div class="chart-item">
            <h2>Reason Analysis</h2>
            <canvas id="reasonPieChart"></canvas>
        </div>
    </div>

    <div class="totals">
        <h3>Total number of students applied: <?php echo $totalStudentsApplied; ?></h3>
    </div>

    <div class="table-container">
        <h2>Course Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courseData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Year Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($yearData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['year']); ?></td>
                        <td><?php echo htmlspecialchars($row['count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Reason Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Reason</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reasonData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Approved Analysis Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Reason</th>
                    <th>Roll Number</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($pgData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['year']); ?></td>
                        <td><? echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['rollnumber']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Rejection Analysis Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Course</th>
                    <th>Reason</th>
                    <th>Roll Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passedData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['rollnumber']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const courseData = <?php echo json_encode($courseData); ?>;
            const yearData = <?php echo json_encode($yearData); ?>;
            const reasonData = <?php echo json_encode($reasonData); ?>;
            const pgData = <?php echo json_encode($pgData); ?>;
            const passedData = <?php echo json_encode($passedData); ?>;

            createPieChart('coursePieChart', 'Course Analysis', courseData, 'course');
            createPieChart('yearPieChart', 'Year Analysis', yearData, 'year');
            createPieChart('reasonPieChart', 'Reason Analysis', reasonData, 'reason');

            function createPieChart(chartId, chartTitle, data, labelKey) {
                const ctx = document.getElementById(chartId).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.map(item => item[labelKey]),
                        datasets: [{
                            label: chartTitle,
                            data: data.map(item => item.count),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            function exportToPDF() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'pt', 'a4');
                const margin = 20;
                const cellPadding = 8;
                const lineHeight = 20;
                const maxWidth = doc.internal.pageSize.width - 2 * margin;
                const minColumnWidth = 100;
                let yPosition = margin;

                function drawCell(x, y, width, height, text, align = 'left') {
                    doc.rect(x, y, width, height);
                    const lines = doc.splitTextToSize(text, width - 2 * cellPadding);
                    const textX = align === 'center' ? x + width / 2 : x + cellPadding;
                    const textOptions = align === 'center' ? { align: 'center' } : {};
                    doc.text(lines, textX, y + lineHeight / 2 + 3, textOptions);
                }

                function calculateColumnWidths(headers, rows) {
                    return headers.map((header, index) => {
                        const maxTextWidth = Math.max(...rows.map(row => doc.getTextWidth(row[header] || '')));
                        return Math.min(Math.max(maxTextWidth + 2 * cellPadding, minColumnWidth), maxWidth / headers.length);
                    });
                }

                function calculateRowHeight(row, columnWidths) {
                    return Math.max(...Object.keys(row).map((key, index) => {
                        const text = row[key] || 'N/A';
                        const lines = doc.splitTextToSize(text, columnWidths[index] - 2 * cellPadding);
                        return lines.length * lineHeight;
                    }));
                }

                function addTableToPDF(title, headers, rows) {
                    doc.setFontSize(12);
                    doc.text(title, margin, yPosition);
                    yPosition += 20;

                    const columnWidths = calculateColumnWidths(headers, rows);

                    headers.forEach((header, index) => {
                        drawCell(margin + columnWidths.slice(0, index).reduce((a, b) => a + b, 0), yPosition, columnWidths[index], lineHeight, header, 'center');
                    });

                    yPosition += lineHeight;

                    rows.forEach(row => {
                        const rowHeight = calculateRowHeight(row, columnWidths);

                        headers.forEach((header, index) => {
                            const cellText = row[header] || 'N/A';
                            const align = header === 'Count' ? 'center' : 'left';
                            drawCell(margin + columnWidths.slice(0, index).reduce((a, b) => a + b, 0), yPosition, columnWidths[index], rowHeight, cellText, align);
                        });

                        yPosition += rowHeight;

                        if (yPosition > doc.internal.pageSize.height - margin - lineHeight) {
                            doc.addPage();
                            yPosition = margin + 40;
                        }
                    });

                    yPosition += 20;
                }

                addTableToPDF('Course Data', ['Course', 'Count'], courseData.map(row => ({
                    Course: row.course,
                    Count: row.count
                })));

                addTableToPDF('Year Data', ['Year', 'Count'], yearData.map(row => ({
                    Year: row.year,
                    Count: row.count
                })));

                addTableToPDF('Reason Data', ['Reason', 'Count'], reasonData.map(row => ({
                    Reason: row.reason,
                    Count: row.count
                })));

                addTableToPDF('Approved Analysis Data', ['Date', 'Course', 'Year', 'Reason', 'Roll Number'], pgData.map(row => ({
                    Date: row.Date,
                    Course: row.course,
                    Year: row.year,
                    Reason: row.reason,
                    'Roll Number': row.rollnumber
                })));

                addTableToPDF('Rejection Analysis Data', ['Date', 'Course', 'Reason', 'Roll Number'], passedData.map(row => ({
                    Date: row.Date,
                    Course: row.course,
                    Reason: row.reason,
                    'Roll Number': row.rollnumber
                })));

                doc.save('analysis_statistics.pdf');
            }

            function exportToCSV(dataSections) {
                let csvContent = "data:text/csv;charset=utf-8,";

                dataSections.forEach(section => {
                    csvContent += `${section.title}\r\n`;

                    if (section.data.length > 0) {
                        csvContent += Object.keys(section.data[0]).map(key => `"${key}"`).join(",") + "\r\n";

                        section.data.forEach(rowArray => {
                            let row = Object.values(rowArray).map(value => `"${value}"`).join(",");
                            csvContent += row + "\r\n";
                        });
                    }

                    csvContent += "\r\n";
                });

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "analysis_statistics.csv");
                document.body.appendChild(link);
                link.click();
            }

            function exportToExcel() {
                const workbook = XLSX.utils.book_new();
                const dataSections = [
                    { title: 'Course Data', data: courseData.map(row => ({ Course: row.course, Count: row.count })) },
                    { title: 'Year Data', data: yearData.map(row => ({ Year: row.year, Count: row.count })) },
                    { title: 'Reason Data', data: reasonData.map(row => ({ Reason: row.reason, Count: row.count })) },
                    {
                        title: 'Approved Analysis Data', data: pgData.map(row => ({
                            Date: row.Date,
                            Course: row.course,
                            Year: row.year,
                            Reason: row.reason,
                            'Roll Number': row.rollnumber
                        }))
                    },
                    {
                        title: 'Rejection Analysis Data', data: passedData.map(row => ({
                            Date: row.Date,
                            Course: row.course,
                            Reason: row.reason,
                            'Roll Number': row.rollnumber
                        }))
                    }
                ];

                dataSections.forEach(section => {
                    const worksheet = XLSX.utils.json_to_sheet(section.data);
                    XLSX.utils.book_append_sheet(workbook, worksheet, section.title);
                });

                XLSX.writeFile(workbook, 'analysis_statistics.xlsx');
            }

            document.getElementById('dropdownContent').addEventListener('click', (event) => {
                if (event.target.tagName === 'A') {
                    const format = event.target.getAttribute('data-format');
                    const dataSections = [
                        { title: 'Course Data', data: courseData.map(row => ({ Course: row.course, Count: row.count })) },
                        { title: 'Year Data', data: yearData.map(row => ({ Year: row.year, Count: row.count })) },
                        { title: 'Reason Data', data: reasonData.map(row => ({ Reason: row.reason, Count: row.count })) },
                        {
                            title: 'Approved Analysis Data', data: pgData.map(row => ({
                                Date: row.Date,
                                Course: row.course,
                                Year: row.year,
                                Reason: row.reason,
                                'Roll Number': row.rollnumber
                            }))
                        },
                        {
                            title: 'Rejection Analysis Data', data: passedData.map(row => ({
                                Date: row.Date,
                                Course: row.course,
                                Reason: row.reason,
                                'Roll Number': row.rollnumber
                            }))
                        }
                    ];

                    switch (format) {
                        case 'csv':
                            exportToCSV(dataSections);
                            break;
                        case 'pdf':
                            exportToPDF();
                            break;
                        case 'excel':
                            exportToExcel();
                            break;
                        default:
                            alert('Invalid format selected.');
                            break;
                    }
                }
            });
        });

    </script>
</body>

</html>