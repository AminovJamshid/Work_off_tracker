<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Off Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: var(--container-bg-color);
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        h1 {
            text-align: center;
        }
        .mode-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            font-size: 24px;
            transition: color 0.3s;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        input[type="datetime-local"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid var(--input-border-color);
            border-radius: 4px;
            background-color: var(--input-bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, border-color 0.3s;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #d1e7dd;
            color: #0f5132;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        button:hover {
            background-color: #badbcc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: var(--item-bg-color);
            margin: 10px 0;
            padding: 10px;
            border: 1px solid var(--item-border-color);
            border-radius: 4px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.5s forwards;
            transition: background-color 0.3s, border-color 0.3s;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid var(--item-border-color);
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: var(--table-header-bg-color);
            color: var(--table-header-text-color);
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        :root {
            --bg-color: #f4f4f9;
            --text-color: #333;
            --container-bg-color: #fff;
            --input-bg-color: #fff;
            --input-border-color: #ccc;
            --item-bg-color: #f9f9f9;
            --item-border-color: #ddd;
            --table-header-bg-color: #e0e0e0;
            --table-header-text-color: #333;
        }
        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --container-bg-color: #1e1e1e;
            --input-bg-color: #2c2c2c;
            --input-border-color: #555;
            --item-bg-color: #2c2c2c;
            --item-border-color: #555;
            --table-header-bg-color: #333;
            --table-header-text-color: #e0e0e0;
        }
    </style>
</head>
<body>
<div class="container">
    <i class="mode-toggle fa fa-moon" onclick="toggleTheme()"></i>
    <h1>Work Off Tracker</h1>
    <div id="message" class="message"></div>
    <form id="workOffForm" action="work_off_tracker2.php" method="post">
        Arrived At <input type="datetime-local" name="arrived_at"><br>
        Leaved At <input type="datetime-local" name="leaved_at"><br>
        <button type="submit">Submit</button>
    </form>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Arrived At</th>
            <th>Leaved At</th>
            <th>Working Duration</th>
            <th>Required Work Off Time</th>
            <th>Entitled Time</th>
            <th>Required Time Sum</th>
            <th>Entitled Time Sum</th>
        </tr>
        </thead>
        <tbody>
        <?php
        class work_off_tracker2 {
            private $pdo;

            public function __construct($servername, $username, $password, $dbname) {
                date_default_timezone_set("Asia/Tashkent");
                $this->pdo = new PDO("mysql:host=localhost;dbname=$dbname", $username, $password);
            }

            private function calculate_seconds_to_hour($sec) {
                return floor($sec / 3600);
            }

            public function addEntry($arrived_at, $leaved_at) {
                if (!empty($arrived_at) && !empty($leaved_at)) {
                    $arrivedat = new DateTime($arrived_at);
                    $leavedat = new DateTime($leaved_at);

                    $work_off_time_sum = 0;
                    $entitled_time_sum = 0;

                    $arrivedatFormatted = $arrivedat->format("Y-m-d H:i:s");
                    $leavedatFormatted = $leavedat->format("Y-m-d H:i:s");

                    $interval = $arrivedat->diff($leavedat);
                    $workingDurationSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

                    $const_work_time = 32400;

                    if ($workingDurationSeconds > $const_work_time){
                        $debted_time = $workingDurationSeconds - $const_work_time;
                        $req_work_off_timee = $this->calculate_seconds_to_hour($debted_time);
                        $work_off_time_sum += $req_work_off_timee;
                    } else if ($workingDurationSeconds < $const_work_time){
                        $debted_time = $const_work_time - $workingDurationSeconds;
                        $entitled = $this->calculate_seconds_to_hour($debted_time);
                        $entitled_time_sum += $entitled;
                    }


                    $query = $this->pdo->query("SELECT * FROM Daily")->fetchAll();

                    $new_entitled_time_sum = 0;
                    $new_work_off_time_sum = 0;
                    foreach ($query as $row) {
                        $new_entitled_time_sum += $row['req_work_off_time_sum'];
                        $new_work_off_time_sum += $row['entitled_time_sum'];
                    }

                    if($work_off_time_sum > $entitled_time_sum){
                        $new_work_off_time_sum = $work_off_time_sum - $entitled_time_sum;
                    }elseif ($work_off_time_sum < $entitled_time_sum) {
                        $new_entitled_time_sum = $entitled_time_sum - $work_off_time_sum;
                    }else{
                        $new_work_off_time_sum = 0;
                        $new_entitled_time_sum = 0;
                    }

                    $workingDurationSeconds = $this->calculate_seconds_to_hour($workingDurationSeconds);

                    $sql = "INSERT INTO Daily (arrived_at, leaved_at, working_duration, req_work_off_time, entitled, req_work_off_time_sum, entitled_time_sum) VALUES (:arrived_at, :leaved_at, :working_duration, :req_work_off_time, :entitled, :req_work_off_time_sum, :entitled_time_sum)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':arrived_at', $arrivedatFormatted);
                    $stmt->bindParam(':leaved_at', $leavedatFormatted);
                    $stmt->bindParam(':working_duration', $workingDurationSeconds);
                    $stmt->bindParam(':req_work_off_time', $entitled);
                    $stmt->bindParam(':entitled', $req_work_off_timee);
                    $stmt->bindParam(':req_work_off_time_sum', $new_entitled_time_sum);
                    $stmt->bindParam(':entitled_time_sum', $new_work_off_time_sum);
                    $stmt->execute();
                    return "Dates successfully added.";
                } else {
                    return "Please fill the gaps!";
                }
            }

            public function displayEntries() {
                $query = $this->pdo->query("SELECT * FROM Daily")->fetchAll();
                foreach ($query as $row) {
                    echo "<tr>
                                <td>{$row["id"]}</td>
                                <td>{$row['arrived_at']}</td>
                                <td>{$row["leaved_at"]}</td>
                                <td>{$row["working_duration"]} Hours</td>
                                <td>{$row["req_work_off_time"]}</td>
                                <td>{$row["entitled"]}</td>
                                <td>{$row["req_work_off_time_sum"]}</td>
                                <td>{$row["entitled_time_sum"]}</td>
                              </tr>";
                }
            }
        }

        $servername = "localhost";
        $username = "root";
        $password = "1234";
        $dbname = "work_off_tracker";

        $workOffTracker = new work_off_tracker2($servername, $username, $password, $dbname);

        if (!empty($_POST["arrived_at"]) && !empty($_POST["leaved_at"])){


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $workOffTracker->addEntry($_POST["arrived_at"], $_POST["leaved_at"]);
            echo "<script>
                        document.getElementById('message').innerText = '$message';
                        document.getElementById('message').classList.add('$message === 'Dates successfully added.' ? 'success' : 'error');
                        document.getElementById('message').style.display = 'block';
                    </script>";
        }
        }

        $workOffTracker->displayEntries();
        ?>
        </tbody>
    </table>
</div>
<script>
    function toggleTheme() {
        const body = document.body;
        const icon = document.querySelector('.mode-toggle');
        const isDark = body.getAttribute('data-theme') === 'dark';
        body.setAttribute('data-theme', isDark ? 'light' : 'dark');
        icon.classList.toggle('fa-sun', isDark);
        icon.classList.toggle('fa-moon', !isDark);
    }

    document.getElementById('workOffForm').addEventListener('submit', function (event) {
        const arrivedAt = document.querySelector('input[name="arrived_at"]').value;
        const leavedAt = document.querySelector('input[name="leaved_at"]').value;
        const messageDiv = document.getElementById('message');

        if (!arrivedAt || !leavedAt) {
            event.preventDefault();
            messageDiv.innerText = 'Please fill the gaps!';
            messageDiv.classList.remove('success');
            messageDiv.classList.add('error');
            messageDiv.style.display = 'block';
        }
    });
</script>
</body>
</html>