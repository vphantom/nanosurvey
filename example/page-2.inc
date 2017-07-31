Ask a question here about age before going further.

<?php

if ($survey->previousAnswer(1) !== 'yes') {
    $survey->skipPage();
};

?>

<?= $survey->newQuestion('radio') ?>
<p>Question text here?</p>
<ul>
<li><?= $survey->radioCheckbox('yes') ?> Yes</li>
<li><?= $survey->radioCheckbox('no') ?> No</li>
</ul>
<?= $survey->endQuestion() ?>

<?= $survey->submitButton() ?>
