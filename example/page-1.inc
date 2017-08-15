Ask a question here about age before going further.

<?= $survey->newQuestion('radio') ?>
<p>Question text here?</p>
<ul>
<li><?= $survey->radioCheckbox('yes') ?> Yes</li>
<li><?= $survey->radioCheckbox('no') ?> No</li>
<li><?= $survey->radioCheckbox('notsure', true) ?> Not sure</li>
</ul>
<?= $survey->endQuestion() ?>

<?= $survey->newQuestion() ?>
<p>How much do you make per year?</p>
<p><?= $survey->textbox() ?></p>
<?= $survey->endQuestion() ?>

<?= $survey->newQuestion() ?>
<p>Question about another thing? (Select all that apply.)</p>
<ul>
<li><?= $survey->checkbox('endorphins') ?> Endorphins</li>
<li><?= $survey->checkbox('testosterone') ?> Testosterone and derivatives</li>
<li><?= $survey->checkbox('aldosterone') ?> Aldosterone and adrenalin derivatives</li>
<li>Other: <?= $survey->textbox() ?></li>
</ul>
<?= $survey->endQuestion() ?>

<?= $survey->newQuestion() ?>
<p>How many months per year does the stuff happen?</p>
<?= $survey->beginSelectOne() ?>
<option value="">Please select
<?= $survey->endSelectOne(0,12) ?>
<?= $survey->endQuestion() ?>

<?= $survey->newQuestion('radio') ?>
Radio problem
<ul>
<li><?= $survey->radioCheckbox('test1') ?> test1</li>
<li><?= $survey->radioCheckbox('test2') ?> test2</li>
<li><?= $survey->radioCheckbox('test3') ?> test3, explain: <?= $survey->textbox() ?></li>
</ul>
<?= $survey->endQuestion() ?>

<?= $survey->submitButton() ?>
