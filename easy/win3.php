<!DOCTYPE html>
<html lang="en">

<head>
  <title>Silent Strings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/x-icon" href="./img/logo.png" />
  <link rel="stylesheet" href="../css/difficulty.css" />
</head>

<body>
  <div class="center">
    <header>
      <h1>˗ˏˋ ´ˎ˗ YOU WON ˗ˏˋ ´ˎ˗</h1>
      <h3>YOU COMPLETED ALL LEVELS!!!</h3>

      <!-- Form for submitting the score -->
      <form method="POST">
        <label for="player_name">Enter your name:</label>
        <input type="text" id="player_name" name="player_name" required />
        <input type="hidden" name="score" value="300" />
        <button type="submit">Submit Score</button>
      </form>
    </header>

    <br />

    <!-- Display message if score is submitted -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_name'], $_POST['score'])) {
      saveScore((int)$_POST['score'], $_POST['player_name']);
      echo "<p>Score submitted successfully! You can now view the leaderboard.</p>";
    }

    // Function to save the score in a cookie
    function saveScore($score, $name)
    {
      if (isset($_COOKIE['scores'])) {
        $scores = json_decode($_COOKIE['scores'], true);
      } else {
        $scores = [];
      }

      $scores[] = [
        'score' => $score,
        'name' => $name,
        'date' => date("Y-m-d H:i:s"),
      ];

      setcookie('scores', json_encode($scores), time() + (86400 * 30), "/");
    }
    ?>

    <!-- Navigation buttons -->
    <a class="button" href="../main.html">← HOME</a>
    <a class="button" href="../leaderboard.php">LEADERBOARD</a>
  </div>
</body>

</html>