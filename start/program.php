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
$programs = json_decode(file_get_contents('../programs.json'), true);
$exercises = json_decode(file_get_contents('../exercises.json'), true);
$muscles = json_decode(file_get_contents('../muscles.json'), true);
$program = $programs[$_REQUEST['id']];
function generateExerciseURL ($exercises, $id) {
    return 'iframe&' . (isset($exercises[$id - 1]) ? 'previous&' : '') . (isset($exercises[$id + 1]) ? 'next&' : '') . 'id=' . ($exercises[$id]['id']) . (isset($exercises[$id]['time']) ? '&time=' . $exercises[$id]['time'] : (isset($exercises[$id]['reps']) ? '&reps=' . $exercises[$id]['reps'] : '') . (isset($exercises[$id]['weight']) ? '&weight=' . $exercises[$id]['weight'] : ''));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/main.css">
    <title>SP | Workout</title>
</head>

<body>
    <main>
        <div class="left">
            <table>
                <tbody>
                    <?php $id = 0; foreach($program['exercises'] as $exercise): ?>
                    <tr onclick="move(<?= $id ?>);">
                        <td class="name"><?= $exercises[$exercise['id']]["name"] ?></span>
                        <td class="repstime"><?= isset($exercise["reps"]) ? $exercise["reps"] . " reps" : (isset($exercise["time"]) ? $exercise["time"] . "s" : "") ?></span>
                        <td class="weight"><?= isset($exercise["weight"]) ? $exercise["weight"] . "kg" : "" ?></span>
                    </tr>
                    <?php $id++; endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="right">
            <iframe src="./exercise.php?<?= generateExerciseURL($program['exercises'], 0) ?>"></iframe>
        </div>
    </main>
    <script>
        var urls = [<?php foreach ($program['exercises'] as $key => $value) echo("'exercise.php?" . generateExerciseURL($program['exercises'], $key) . "', "); ?>];
        var id = 0;
        const iframe = document.querySelector('iframe');
        window.addEventListener('message', function(event) {
          if (event.data === 'next') {
              move(id + 1);
          }
          if (event.data === 'previous') {
              move(id - 1);
          }
        });
        function move(newId) {
              id = newId;
              iframe.src = urls[id];
        }
    </script>
    <style>
        html {
            font-family: monospace;
            overflow: hidden;
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
            grid-template-columns: 30% 70%;
        }
        @media (max-width: 768px) {
            main {
                display: block;
            }
            .left {
                display: none;
            }
        }
        div {
            padding: 10px;
            width: 100%;
            height: 100%;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            overflow-y: scroll;
            overflow-x: hidden;
        }
        .right {
            padding: 0;
        }
        .left {
            border-right: 2px solid black;
        }
        .left td {
            margin: 0;
            padding: 3px;
        }
        .left tr {
            cursor: pointer;
        }
        .left tr:hover {
            background-color: #ccc;
        }
        .left tr.selected {
            background-color: #ccccff;
        }
        .bottom {
            grid-area: 1 / 1 / 0 / 1;
        }
        table {
            width: 100%;
        }
        .name {
            font-weight: bold;
            min-width: 100px;
        }
        .repstime {
            color: blue;
            margin: 0 15px;
            text-align: right;
        }
        .weight {
            color: red;
            font-weight: bold;
            text-align: right;
        }
    </style>
</body>

</html>