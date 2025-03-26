<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حاسبة أعمار الطلبة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .container {
            max-width: 600px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .button-container {
            background-color: #e0e0e0;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            border: 1px solid #bdbdbd;
        }
        .result {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .red { background-color: #ffcccc; color: #d80000; }
        .green { background-color: #ccffcc; color: #008000; }
        .yellow { background-color: #ffffcc; color: #b8860b; }
        .blue { background-color: #cce5ff; color: #004085; }
        .highlight { background-color: #a3daff !important; font-weight: bold; }
        h2 { color: #007bff; }
        table { width: 100%; margin-top: 20px; border-radius: 10px; overflow: hidden; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        .footer { margin-top: 20px; font-size: 16px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4"><i class="fa-solid fa-user-graduate"></i> حاسبة أعمار الطلبة</h2>
        <form method="post">
            <div class="input-group mb-3">
                <span class="input-group-text bg-primary text-white"><i class="fa-solid fa-calendar"></i></span>
                <input type="text" id="dob" name="dob" class="form-control" placeholder="أدخل تاريخ الميلاد" required readonly>
            </div>
            <div class="button-container">
                <button type="submit" class="btn w-100">
                    <i class="fa-solid fa-calculator"></i> احسب العمر
                </button>
            </div>
        </form>
        
        <?php
        function getHijriDate($date) {
            $formatter = new IntlDateFormatter(
                "ar_SA@calendar=islamic",
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'Asia/Riyadh',
                IntlDateFormatter::TRADITIONAL,
                "d MMMM yyyy"
            );
            return "<span class='text-primary'>" . $formatter->format($date) . "</span>";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($dob) {
                setcookie("savedDOB", $dob, time() + (86400 * 30), "/");
                $birthDate = new DateTime($dob);
                $hijriDate = getHijriDate($birthDate);
                
                $years = [
                    "2025-08-24" => "العام الدراسي القادم 2025",
                    "2026-08-23" => "السنة الدراسية 2026",
                    "2027-08-22" => "السنة الدراسية 2027",
                    "2028-08-19" => "السنة الدراسية 2028"
                ];
                
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>السنة الدراسية</th><th>حالة القبول</th></tr></thead><tbody>";
                
                foreach ($years as $startDate => $yearLabel) {
                    $startDateObj = new DateTime($startDate);
                    $ageInterval = $birthDate->diff($startDateObj);
                    $totalAgeDays = $ageInterval->days;
                    
                    if ($totalAgeDays >= 8383) {
                        $message = "<span class='text-danger'>الطالب أكبر من السن النظامي</span>";
                        $class = "red";
                    } elseif ($totalAgeDays >= 2100) {
                        $message = "<span class='text-success'>الصف الأول الابتدائي بلا شروط</span>";
                        $class = "green";
                    } elseif ($totalAgeDays >= 2008) {
                        $message = "<span class='text-primary'>الصف الأول الابتدائي بشرط حصوله على روضة معتمدة</span>";
                        $class = "blue";
                    } elseif ($totalAgeDays >= 1825) {
                        $message = "<span class='text-warning'>المستوى الثالث تمهيدي</span>";
                        $class = "yellow";
                    } elseif ($totalAgeDays >= 1460) {
                        $message = "<span class='text-warning'>المستوى الثاني بحسب وجود شواغر في الروضة</span>";
                        $class = "yellow";
                    } elseif ($totalAgeDays >= 1095) {
                        $message = "<span class='text-primary'>المستوى الأول في المدارس الخاصة فقط</span>";
                        $class = "blue";
                    } else {
                        $message = "<span class='text-danger'>للأسف الطالب أقل من السن القانوني</span>";
                        $class = "red";
                    }

                    $rowClass = ($yearLabel == "العام الدراسي القادم 2025") ? "highlight" : "";
                    echo "<tr class='$rowClass'><td>$yearLabel</td><td class='result $class'>$message</td></tr>";
                }
                
                echo "</tbody></table>";
                echo "<div class='result'>التاريخ الهجري لميلاد الطالب: $hijriDate</div>";
            }
        }
        ?>
    </div>

    <div class="footer">
        <p>للانتقال لصفحة تسجيل الطلبة <a href="https://preregistration.moe.gov.sa/Portal/" target="_blank">انقر هنا</a></p>
        <p><strong>مع تحيات بو عبدالإله</strong></p>
    </div>

    <script>
        flatpickr("#dob", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            minDate: "2000-01-01",
            locale: "ar"
        });
        
        document.addEventListener("DOMContentLoaded", function() {
            let savedDOB = document.cookie.split('; ').find(row => row.startsWith('savedDOB='))?.split('=')[1];
            if (savedDOB) {
                document.getElementById("dob").value = savedDOB;
            }
        });
    </script>
</body>
</html>
