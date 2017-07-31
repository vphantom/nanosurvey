<?php

require_once 'NanoSurvey.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey</title>
</head>
<body>

<h1>Survey</h1>

<?php

$survey = new NanoSurvey('/tmp/answers.csv');
echo $survey->page();

?>

</body>
</html>
