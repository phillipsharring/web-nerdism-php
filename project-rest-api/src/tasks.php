<?php
declare(strict_types=1);

/** One task as the API shows it. */
function present(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'title' => $row['title'],
        'done' => (bool) $row['done'],
        'url' => '/api/tasks/' . $row['id'],
    ];
}

/**
 * Checks a new or changed task. Returns the clean fields, or a list of what's
 * wrong, field by field, for a 422.
 *
 * @return array{0: array<string, mixed>, 1: array<string, string>}
 */
function validateTask(array $data, bool $partial): array
{
    $clean = [];
    $errors = [];
    if (array_key_exists('title', $data) || !$partial) {
        $title = is_string($data['title'] ?? null) ? trim($data['title']) : '';
        if ($title === '' || mb_strlen($title) > 200) {
            $errors['title'] = 'A title, from 1 to 200 characters.';
        } else {
            $clean['title'] = $title;
        }
    }
    if (array_key_exists('done', $data)) {
        if (!is_bool($data['done'])) {
            $errors['done'] = 'true or false.';
        } else {
            $clean['done'] = $data['done'] ? 1 : 0;
        }
    }
    foreach (array_diff(array_keys($data), ['title', 'done']) as $unknown) {
        $errors[$unknown] = 'Not a field a task has.';
    }

    return [$clean, $errors];
}
