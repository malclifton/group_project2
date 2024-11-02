<?php




session_start();
$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$WON = false;

//testing variables
$guess = "HANGMAN";
$maxLetters = strlen($guess) - 1;
$responses = ["H", "G", "A"];

$bodyParts = ["empty", "head", "neck", "body", "arm", "arms", "leg", "full"];
//change to get words from a text file
$words = [
    "SUNFLOWER",
    "TULIP",
    "PEONY",
    "AZALEA",
    "ORCHID",
    "BEGONIA",
    "DAFFODIL",
];

//respond to guesses
function getCurrentPicture($part)
{
    return "../img/hangman_" . $part . ".png";
}
function startGame() {}
//restarts the game and clears session
function restartGame()
{
    session_destroy();
    session_start();
    markGameAsNew();
    $_SESSION["parts"] = getParts();
    unset($_SESSION["responses"], $_SESSION["word"]);
}
//get all parts
function getParts()
{
    global $bodyParts;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $bodyParts;
}
//add parts
function addPart()
{
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
}

//get current body part
function getCurrentPart()
{
    $parts = getParts();
    return $parts[0];
}
function getCurrentWord()
{
    //return "HANGMAN";
    global $words;
    if (!isset($_SESSION["word"]) && empty($_SESSION["word"])) {
        $key = array_rand($words);
        $_SESSION["word"] = $words[$key];
    }
    return $_SESSION["word"];
}
//user response 
//get user response
function getCurrentResponses()
{
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}
function addResponse($letter)
{
    $reponses = getCurrentResponses();
    array_push($reponses, $letter);
    $_SESSION["responses"] = $reponses;
}
//check if letter pressed is right
function isLetterCorrect($letter)
{
    $word = getCurrentWord();
    $max = strlen($word) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if ($letter == $word[$i]) {
            return true;
        }
    }
    return false;
}
//is the guess correct
function isWordCorrect()
{
    $guess = getCurrentWord();
    $reponses = getCurrentResponses();
    $max = strlen($guess) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if (!in_array($guess[$i], $reponses)) {
            return false;
        }
    }
    return true;
}
function isBodyComplete()
{
    $parts = getParts();
    if (count($parts) <= 1) {
        return true;
    }
    return false;
}

//manage game session

//determine if game is complete
function gameComplete()
{
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] : false;
}
//game complete
function markGameAsComplete()
{
    $_SESSION["gamecomplete"] = true;
}
//new game
function markGameAsNew()
{
    $_SESSION["gamecomplete"] = false;
}

//detect restart
if (isset($_GET['start'])) {
    restartGame();
}
//detect if key is pressed
if (isset($_GET['kp'])) {
    $currentPressedKey = isset($_GET['kp']) ? $_GET['kp'] : null;
    //for correct key
    if ($currentPressedKey && isLetterCorrect($currentPressedKey) && !isBodyComplete() && !gameComplete()) {
        addResponse($currentPressedKey);
        if (isWordCorrect()) {
            $WON = true;
            markGameAsComplete();
            restartGame();
            //   if ($WON && gameComplete()) {
            //     if (isset($_POST['player_name'])) {
            //       $playerName = $_POST['player_name'];
            //     saveScore(100, $playerName);
            //}
            //  }
            header('Location: ./win_l2.php'); //goes to win page
            exit();
        }
    } else {
        //add body parts to display
        if (!isBodyComplete()) {
            addPart();
            if (isBodyComplete()) {
                markGameAsComplete(); //lost condition
                restartGame();
                header('Location: ./lose_l2.html');
                exit();
            }
        } else {
            markGameAsComplete(); //lost condition
            restartGame();
            header('Location: ./lose_l2.html');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Silent Strings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="../img/logo.png" />
    <link rel="stylesheet" href="../css/levels.css" />

</head>

<body>
    <audio controls autoplay hidden loop>
        <source src="./img/lofi.mp3" type="audio/mpeg" />
        Your browser does not support the audio element.
    </audio>
    <br><br><br><br><br>
    <div class="container">
        <div class="content-wrapper">
            <div class="hangman_img">
                <img src="<?php echo getCurrentPicture(getCurrentPart()); ?>">
            </div>
            <div class="game-info">
                <?Php if (gameComplete()): ?>
                <?php endif; ?>
                <?php if ($WON  && gameComplete()): ?>
                <?php elseif (!$WON && gameComplete()):
                ?>
                <?php endif; ?>

                <!--display current guesses-->
                <br><br><br><br><br>
                <div class="word">
                    <?php
                    $guess = getCurrentWord();
                    $maxLetters = strlen($guess) - 1;
                    for ($j = 0; $j <= $maxLetters; $j++):
                        $l = getCurrentWord()[$j]; ?>
                        <?php if (in_array($l, getCurrentResponses())): ?>
                            <span><?php echo $l; ?></span>
                        <?php else: ?>
                            <span>&nbsp;&nbsp;&nbsp;</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <br>
                Hint: Types of Flowers
                <br>
                <div class="keypad">
                    <form method="get">
                        <?php
                        $max = strlen($letters) - 1;
                        for ($i = 0; $i < $max; $i++) {
                            echo "<button type='submit' name='kp' value='" . $letters[$i] . "'>" . $letters[$i] . "</button>";
                            if ($i % 8 == 0 && $i > 0) {
                                echo "<br>";
                            }
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