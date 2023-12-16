<?php
session_start();
if (!isset($_SESSION['token']))
{
    $state = base64_encode(random_bytes(32));
    $_SESSION['state'] = $state;
    header('Location: https://simonpucheu.000webhostapp.com/auth/authorize.php?response_type=code&client_id=workout&redirect_uri=https://simonpucheu.000webhostapp.com/WorkOut/authorize.php&state=' . urlencode($state));
    die;
}
$token = $_SESSION['token'];
$context = stream_context_create([
    'http' => [
        'header' => "Authorization: Bearer " . $token
    ]
]);
//$data = json_decode(json_decode(file_get_contents('https://simonpucheu.000webhostapp.com/api/client/data/get.php?client_id=workout&client_secret=workoutpassword', false, $context), true)['data'], true);
if (!isset($_REQUEST['id'])) {
    die;
}
if (isset($_REQUEST['reps'])) {
    global $reps;
    $reps = $_REQUEST['reps'];
}
else if (isset($_REQUEST['time'])) {
    global $time;
    $time = $_REQUEST['time'];
}
if (isset($_REQUEST['weight'])) {
    global $weight;
    $weight = $_REQUEST['weight'];
}
$exercises = json_decode(file_get_contents('../exercises.json'), true);
$muscles = json_decode(file_get_contents('../muscles.json'), true);
$exercise = $exercises[$_REQUEST['id']];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
    <script type="text/javascript" src="setTimer.js"></script>
    <title>SP | Work Out</title>
</head>

<body>
    <main>
        <div>
            <h1><?= $exercise['name'] ?></h1>
            <img src="https://static.strengthlevel.com/images/illustrations/<?= strtolower(str_replace(' ', '-', $exercise['name'])) ?>-1000x1000.jpg" alt="How to" width="300">
            <h2><?= "<span style=\"color: blue;\">" . (isset($time) ? $time . 's' : (isset($reps) ? $reps . ' reps' : "")) . "</span>" ?><?= isset($weight) ? " | <span style=\"color: red;\">" . $weight . "kg</span>" : "" ?></h2>
        </div>
        <div>
            <ul style="color: red; font-weight: bold;">
                <?php foreach($exercise["muscles"]["primary"] as $muscle): ?>
                <li><?= $muscles[$muscle]["name"] ?></li>
                <?php endforeach; ?>
            </ul>
            <ul style="color: green;">
                <?php foreach($exercise["muscles"]["secondary"] as $muscle): ?>
                <li><?= $muscles[$muscle]["name"] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <button id="previous" <?= isset($_REQUEST['previous']) ? 'onclick="window.parent.postMessage(\'previous\', \'*\');"' : 'disabled' ?>>
                <span class="material-symbols-outlined">skip_previous</span>
            </button>
            <button id="pause" class="ms">
                <span class="material-symbols-outlined">pause</span>
            </button>
            <button id="play" class="ms hide">
                <span class="material-symbols-outlined">play_arrow</span>
            </button>
            <button id="done" class="ms hide">
                <span class="material-symbols-outlined">done</span>
            </button>
            <button id="next" <?= isset($_REQUEST['next']) ? 'onclick="window.parent.postMessage(\'next\', \'*\');"' : 'disabled' ?>>
                <span class="material-symbols-outlined">skip_next</span>
            </button>
        </div>
        <div>
            <span id="time"></span>
        </div>
    </main>
    <script>
        var exercise = JSON.parse('<?= json_encode($exercise) ?>');
        exercise['<?= isset($_REQUEST['time']) ? 'time' : (isset($_REQUEST['reps']) ? 'reps' : '') ?>'] = '<?= isset($_REQUEST['time']) ? $_REQUEST['time'] : (isset($_REQUEST['reps']) ? $_REQUEST['reps'] : '') ?>';
        
        function speak (text) {
            var utt = new SpeechSynthesisUtterance();
            utt.text = text;
            utt.lang = 'en';
            window.speechSynthesis.speak(utt);
        }
        
        window.onload = function () {
            var runTimer;
            var prepTimer;
            
            const timeSpan = document.getElementById('time');
            const pause = document.getElementById('pause');
            const play = document.getElementById('play');
            const done = document.getElementById('done');
            
            pause.onclick = function () {
                speak('exercise paused');
                prepTimer.pause();
                if (runTimer instanceof Object && runTimer !== null)
                    runTimer.pause();
                pause.classList.add('hide');
                play.classList.remove('hide');
            }
            play.onclick = function () {
                speak('exercise resumed');
                prepTimer.play();
                if (runTimer instanceof Object && runTimer !== null)
                    runTimer.play();
                play.classList.add('hide');
                pause.classList.remove('hide');
            }
            done.onclick = function () {
                speak('exercise done');
                <?php if (isset($_REQUEST['iframe'])): ?>
                window.parent.postMessage('next', '*');
                <?php else: ?>
                console.log('finished');
                <?php endif; ?>
            }
            
            if (exercise['prep_time'] == undefined) exercise['prep_time'] = 3;
            
            speak(exercise['name']);
            speak(exercise['time'] ? exercise['time'] + ' seconds' : exercise['reps'] + ' times');
            speak(exercise['weight'] ? exercise['weight'] + ' kilogram' : '');
            
            prepTimer = setDescTimer(function (time) {
                timeSpan.innerHTML = time / 1000;
            }, exercise['prep_time'] * 1000);
            
            prepTimer.onfinish(function () {
                if (exercise['time'] !== undefined) {
                    runTimer = setDescTimer(function (time) {
                        timeSpan.innerHTML = time / 1000;
                    }, exercise['time'] * 1000);
                    
                    runTimer.onfinish(function () {
                        <?php if (isset($_REQUEST['iframe'])): ?>
                        window.parent.postMessage('next', '*');
                        <?php else: ?>
                        console.log('finished');
                        <?php endif; ?>
                    });
                }
                else if (exercise['reps'] !== undefined) {
                    time.innerHTML = exercise['reps'] + '×';
                    pause.classList.add('hide');
                    play.classList.add('hide');
                    done.classList.remove('hide');
                }
            });
        }
    </script>
    <style>
        html {
            font-family: monospace;
        }
        main {
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-rows: repeat(4, 1fr);
        }
        main * {
            text-align: center;
        }
        main h1 {
            margin: 0;
            padding: 0;
            font-size: 6em;
        }
        main h2 {
            margin: 0;
            padding: 0;
            font-size: 3em;
        }
        main ul {
            list-style-type: none;
            padding: 0;
        }
        main span#time {
            font-size: 11em;
            display: block;
        }
        button:disabled {
            color: #ccc!important;
            background-color: #eee!important;
            border-color: #aaa!important;
            cursor: default!important;
        }
        #pause, #play, #done {
            padding: 0;
            background-color: #0000ff;
            border: none;
            border-radius: 50%;
            width: 9em;
            height: 9em;
            color: white;
            cursor: pointer;
        }
        #next, #previous {
            padding: 0;
            background: none;
            border: 1px solid #0000ff;
            border-radius: 50%;
            width: 6em;
            height: 6em;
            color: black;
            cursor: pointer;
        }
        div {
            padding: 10px;
            width: 100%;
            height: 100%;
        }
        span {
            display: inline-block;
        }
        .name {
            font-weight: bold;
            min-width: 100px;
        }
        .repstime {
            color: blue;
            margin: 0 15px;
            min-width: 50px;
        }
        .weight {
            color: red;
            font-weight: bold;
        }
    </style>
</body>

</html>