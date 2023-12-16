<?php
if (isset($_REQUEST))
{
    if (isset($_REQUEST['json']))
    {
        $exercises = json_decode(file_get_contents('exercises.json'), true);
        $input = json_decode(urldecode($_REQUEST['json']), true);
        array_push($exercises, $input);
        file_put_contents('exercises.json', json_encode($exercises));
        echo('All good');
    }
}
?>