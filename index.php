<?php
require_once __DIR__.'/src/env.php';
session_start();
if (!isset($_SESSION['token']))
{
    $state = base64_encode(random_bytes(32));
    $_SESSION['state'] = $state;
    header('Location: https://simonpucheu.000webhostapp.com/auth/authorize.php?response_type=code&client_id=' . $_ENV['CLIENT_ID'] . '&redirect_uri=' . $_ENV['AUTH'] . '&state=' . urlencode($state));
    die;
}
$token = $_SESSION['token'];
$context = stream_context_create([
    'http' => [
        'header' => "Authorization: Bearer " . $token
    ]
]);
$user = json_decode(file_get_contents('https://simonpucheu.000webhostapp.com/api/user/get.php?key=name', false, $context), true);
$data = json_decode(json_decode(file_get_contents('https://simonpucheu.000webhostapp.com/api/client/data/get.php?client_id=' . $_ENV['CLIENT_ID'] . '&client_secret=' . $_ENV['CLIENT_SECRET'] . '', false, $context), true)['data'], true);

$programs = json_decode(file_get_contents('programs.json'), true);
$exercises = json_decode(file_get_contents('exercises.json'), true);
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
    <h1>Hello <?= $user['name'] ?></h1>
    <h2>Welcome to Workout!</h2>
    <?php foreach ($programs as $id => $program): ?>
    <ul>
        <li>
            <h3><?= $program['name'] ?></h3>
            <h4 style="font-weight: bold; color: <?= ['#0000ff', '#00ff00', '#ffa500', '#ff3636', '#d50000'][$program['difficulty']] ?>;"><?= ['Relax', 'Easy', 'Medium', 'Hard', 'Expert'][$program['difficulty']] ?></h4>
            <a href="./start/program.php?id=<?= $id ?>">Start program</a>
        </li>
    </ul>
    <?php endforeach; ?>
    <style>
        li {
            padding: 10px;
            width: fit-content;
            border-radius: 10px;
            border: 2px solid blue;
        }
    </style>
</body>

</html>