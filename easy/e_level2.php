<?php
session_start();
$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$WON = false;

$words = ["CAT", "DOG", "BEAR", "LION", "FISH"];
$bodyParts = ["empty", "head", "neck", "body", "arm", "arms", "leg", "full"];

function getCurrentPicture($part) {
    return "../img/hangman_" . $part . ".png";
}

function restartGame() {
    session_destroy();
    session_start();
    markGameAsNew();
    $_SESSION["parts"] = getParts();
    unset($_SESSION["responses"], $_SESSION["word"]);
}

function getParts() {
    global $bodyParts;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $bodyParts;
}

function addPart() {
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
}

function getCurrentPart() {
    $parts = getParts();
    return $parts[0];
}

function getCurrentWord() {
    global $words;
    if (!isset($_SESSION["word"]) && empty($_SESSION["word"])) {
        $key = array_rand($words);
        $_SESSION["word"] = $words[$key];
    }
    return $_SESSION["word"];
}

function getCurrentResponses() {
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addResponse($letter) {
    $responses = getCurrentResponses();
    array_push($responses, $letter);
    $_SESSION["responses"] = $responses;
}

function isLetterCorrect($letter) {
    $word = getCurrentWord();
    return strpos($word, $letter) !== false;
}

function isWordCorrect() {
    $guess = getCurrentWord();
    $responses = getCurrentResponses();
    foreach (str_split($guess) as $char) {
        if (!in_array($char, $responses)) {
            return false;
        }
    }
    return true;
}

function isBodyComplete() {
    return count(getParts()) <= 1;
}

function gameComplete() {
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] : false;
}

function markGameAsComplete() {
    $_SESSION["gamecomplete"] = true;
}

function markGameAsNew() {
    $_SESSION["gamecomplete"] = false;
}

if (isset($_GET['start'])) {
    restartGame();
}

if (isset($_GET['kp'])) {
    $currentPressedKey = $_GET['kp'];
    if (isLetterCorrect($currentPressedKey) && !isBodyComplete() && !gameComplete()) {
        addResponse($currentPressedKey);
        if (isWordCorrect()) {
            $WON = true;
            markGameAsComplete();
            restartGame();
            header('Location: ../easy/win2.php');
            exit();
        }
    } else {
        if (!isBodyComplete()) {
            addPart();
            if (isBodyComplete()) {
                markGameAsComplete();
                restartGame();
                header('Location: ../easy/lose2.html');
                exit();
            }
        } else {
            markGameAsComplete();
            restartGame();
            header('Location: ../easy/lose2.html');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hangman Game - Level 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../css/levels.css" />
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <div class="hangman_img">
                <img src="<?php echo getCurrentPicture(getCurrentPart()); ?>">
            </div>
            <div class="game-info">
                <div class="word">
                    <?php
                    $guess = getCurrentWord();
                    foreach (str_split($guess) as $char):
                        echo in_array($char, getCurrentResponses()) ? "<span>$char</span>" : "<span>&nbsp;&nbsp;&nbsp;</span>";
                    endforeach;
                    ?>
                </div>
                <br>
                Hint: Animal
                <div class="keypad">
                    <form method="get">
                        <?php
                        foreach (str_split($letters) as $letter) {
                            echo "<button type='submit' name='kp' value='$letter'>$letter</button>";
                        }
                        ?>
                        <br><br>
                        <button type="submit" name="start">Restart Game</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>