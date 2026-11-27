<?php

declare(strict_types=1);

// One class, one vague name, and it quietly does everything: checks who's
// asking, checks the input, enforces a rule, saves, and sends email.
class PostManager
{
    private array $posts = [];

    public function process(array $data, string $user): string
    {
        if (($data['author'] ?? '') !== $user) {
            return 'Refused: only the author can save this post.';
        }
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return 'Refused: a post needs a title.';
        }
        foreach ($this->posts as $post) {
            if ($post['slug'] === $data['slug']) {
                return 'Refused: that slug is taken.';
            }
        }
        $this->posts[] = ['title' => $title, 'slug' => $data['slug'], 'author' => $data['author']];
        echo "Emailing subscribers: new post \"$title\"\n";

        return 'Saved.';
    }
}

$manager = new PostManager();
echo $manager->process(['title' => 'Hello', 'slug' => 'hello', 'author' => 'alice'], 'alice'), "\n";
echo $manager->process(['title' => 'Again', 'slug' => 'hello', 'author' => 'alice'], 'alice'), "\n";
echo $manager->process(['title' => ' ', 'slug' => 'blank', 'author' => 'alice'], 'alice'), "\n";
echo $manager->process(['title' => 'Sneaky', 'slug' => 'sneaky', 'author' => 'alice'], 'bob'), "\n";
