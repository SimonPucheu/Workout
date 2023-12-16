<?php
$programs = json_decode(file_get_contents('programs.json'), true);
$exercises = json_decode(file_get_contents('exercises.json'), true);
?>

<?php foreach ($programs as $program): ?>
<div>
    <h2><?= $program['name'] ?></h2>
    <h3>Exercises</h3>
    <table>
        <tbody>
            <?php $id = 0; foreach($program['exercises'] as $exercise): ?>
            <tr onclick="move(<?= $id ?>);">
                <td class="name"><a href="./exercises/<?= strtolower(str_replace(' ', '-', $exercises[$exercise['id']]["name"])) ?>"><?= $exercises[$exercise['id']]["name"] ?></a></span>
                <td class="repstime"><?= isset($exercise["reps"]) ? $exercise["reps"] . " reps" : (isset($exercise["time"]) ? $exercise["time"] . "s" : "") ?></span>
                <td class="weight"><?= isset($exercise["weight"]) ? $exercise["weight"] . "kg" : "" ?></span>
            </tr>
            <?php $id++; endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<style>
    html {
        font-family: monospace;
    }
    div {
        padding: 10px;
        margin-bottom: 30px;
    }
    span {
        display: inline-block;
    }
    td {
        margin: 0;
        padding: 3px;
    }
    tr {
        cursor: default;
    }
    tr:hover {
        background-color: #ccc;
    }
    tr.selected {
        background-color: #ccccff;
    }
    table {
        width: 100%;
    }
    .name {
        font-weight: bold;
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