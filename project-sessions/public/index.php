<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/session.php';

$fruit = [
    'apple' => 'Apple',
    'banana' => 'Banana',
    'cherry' => 'Cherry',
    'damson' => 'Damson',
    'elderberry' => 'Elderberry',
];

start_session();
$_SESSION['shortlist'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $item = $_POST['item'] ?? '';

    if ($action === 'start-over') {
        end_session();
        header('Location: /', true, 303);
        exit;
    }

    // Never trust input: the item has to be one of ours.
    if (!is_string($item) || !array_key_exists($item, $fruit)) {
        flash('Pick something from the list.');
    } elseif ($action === 'add') {
        $_SESSION['shortlist'][$item] = true;
        flash("Added {$fruit[$item]}.");
    } elseif ($action === 'remove') {
        unset($_SESSION['shortlist'][$item]);
        flash("Removed {$fruit[$item]}.");
    }

    header('Location: /', true, 303);
    exit;
}

$message = flash();
$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Shortlist</title>
<?php if ($message !== null): ?>
    <p role="status"><?= $e($message) ?></p>
<?php endif; ?>

<h1>Your Shortlist</h1>
<?php if (!$_SESSION['shortlist']): ?>
    <p>Nothing yet.</p>
<?php else: ?>
    <ul>
        <?php foreach (array_keys($_SESSION['shortlist']) as $key): ?>
            <li><?= $e($fruit[$key]) ?>
                <form method="post" style="display:inline">
                    <input type="hidden" name="item" value="<?= $e($key) ?>">
                    <button type="submit" name="action" value="remove">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Add Something</h2>
<form method="post">
    <select name="item">
        <?php foreach ($fruit as $key => $label): ?>
            <option value="<?= $e($key) ?>"><?= $e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="action" value="add">Add</button>
</form>

<form method="post">
    <button type="submit" name="action" value="start-over">Start over</button>
</form>
