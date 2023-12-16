<?php
$exercises = json_decode(file_get_contents('exercises.json'), true);
$muscles = json_decode(file_get_contents('muscles.json'), true);
?>

<?php foreach ($exercises as $id => $exercise): ?>
<div>
    <h2><?= $id ?>. <?= $exercise['name'] ?></h2>
    <img src="https://static.strengthlevel.com/images/illustrations/<?= strtolower(str_replace(' ', '-', $exercise['name'])) ?>-1000x1000.jpg" alt="How to" width="200">
    <h3>Muscles worked</h3>
    <h4>Primary Muscles</h4>
    <ul>
        <?php foreach($exercise["muscles"]["primary"] as $muscle): ?>
        <li><?= $muscles[$muscle]["name"] ?></li>
        <?php endforeach; ?>
    </ul>
    <h4>Secondary Muscles</h4>
    <ul>
        <?php foreach($exercise["muscles"]["secondary"] as $muscle): ?>
        <li><?= $muscles[$muscle]["name"] ?></li>
        <?php endforeach; ?>
    </ul>
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
</style>