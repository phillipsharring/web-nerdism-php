<?php
declare(strict_types=1);

use App\Validator;

require dirname(__DIR__) . '/vendor/autoload.php';

$tickets = ['standard' => 'Standard', 'student' => 'Student', 'supporter' => 'Supporter'];

$v = new Validator($_POST);
$values = ['name' => '', 'email' => '', 'age' => '', 'ticket' => ''];
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $v->text('name', 'Name', 100);
    $email = $v->email('email', 'Email');
    $age = $v->int('age', 'Age', 13, 120);
    $ticket = $v->oneOf('ticket', 'Ticket', array_keys($tickets));

    $done = $v->passes();

    // Put back what they typed, so a mistake doesn't mean starting over.
    foreach (array_keys($values) as $field) {
        $values[$field] = is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
    }
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// The attributes that tie a field to its error, for screen readers too.
$invalid = fn (string $field): string => $v->error($field) !== null
    ? ' aria-invalid="true" aria-describedby="' . $field . '-error"'
    : '';
$error = fn (string $field): string => $v->error($field) !== null
    ? '<span id="' . $field . '-error">' . $e($v->error($field)) . '</span>'
    : '';
?>
<!doctype html>
<meta charset="utf-8">
<title>Register</title>
<?php if ($done): ?>
    <p>You're registered, <?= $e($name) ?>: one <?= $e($tickets[$ticket]) ?> ticket.</p>
<?php else: ?>
    <?php if ($v->errors()): ?>
        <p>Please fix the <?= count($v->errors()) === 1 ? 'problem' : count($v->errors()) . ' problems' ?> below.</p>
    <?php endif; ?>
    <form method="post" novalidate>
        <p><label>Name <input name="name" value="<?= $e($values['name']) ?>"<?= $invalid('name') ?>></label> <?= $error('name') ?></p>
        <p><label>Email <input type="email" name="email" value="<?= $e($values['email']) ?>"<?= $invalid('email') ?>></label> <?= $error('email') ?></p>
        <p><label>Age <input name="age" inputmode="numeric" value="<?= $e($values['age']) ?>"<?= $invalid('age') ?>></label> <?= $error('age') ?></p>
        <p><label>Ticket
            <select name="ticket"<?= $invalid('ticket') ?>>
                <option value="">Pick one</option>
                <?php foreach ($tickets as $value => $label): ?>
                    <option value="<?= $e($value) ?>"<?= $value === $values['ticket'] ? ' selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select></label> <?= $error('ticket') ?></p>
        <p><button type="submit">Register</button></p>
    </form>
<?php endif; ?>
