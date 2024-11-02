<?php
function displayLeaderboard()
{
    if (isset($_COOKIE['scores'])) {
        $scoresData = $_COOKIE['scores'];
        $scores = json_decode($scoresData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p>Error decoding JSON: " . json_last_error_msg() . "</p>";
            return;
        }

        if (is_array($scores)) {
            usort($scores, function ($a, $b) {
                return $b['score'] - $a['score'];
            });

            echo "<table border='1'>";
            echo "<tr><th>Rank</th><th>Name</th><th>Score</th><th>Date</th></tr>";

            for ($i = 0; $i < min(10, count($scores)); $i++) {
                $rank = $i + 1;
                echo "<tr><td>{$rank}</td><td>{$scores[$i]['name']}</td><td>{$scores[$i]['score']}</td><td>{$scores[$i]['date']}</td></tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Error: Scores data is not in the expected format.</p>";
        }
    } else {
        echo "<p>No scores yet. Play a game to make it to the leaderboard!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Leaderboard</title>
    <link rel="stylesheet" href="./css/difficulty.css" />
</head>

<body>
    <div class="center">
        <h1>LEADERBOARD</h1>
        <?php displayLeaderboard(); ?>
        <br><br>
        <a class="button" href="./main.html">Home</a>
    </div>
</body>

</html>