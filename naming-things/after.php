<?php

declare(strict_types=1);

// The same behavior, split into pieces, each named for what it is.

// A record: a thing with an identity. A noun.
final class Post
{
    public string $title;
    public string $slug;
    public string $author;

    public function __construct(string $title, string $slug, string $author)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->author = $author;
    }
}

// A policy: may this person do this?
final class PostPolicy
{
    public function maySave(string $user, array $data): bool
    {
        return ($data['author'] ?? '') === $user;
    }
}

// Validation: is this input acceptable? Never trust input.
final class PostInput
{
    public function problem(array $data): ?string
    {
        if (trim((string) ($data['title'] ?? '')) === '') {
            return 'a post needs a title.';
        }

        return null;
    }
}

// Where posts are kept, and the rule that must always hold there, however a
// post arrives: no two posts share a slug. That rule is an invariant.
final class PostsTable
{
    private array $posts = [];

    public function add(Post $post): bool
    {
        foreach ($this->posts as $existing) {
            if ($existing->slug === $post->slug) {
                return false;
            }
        }
        $this->posts[] = $post;

        return true;
    }
}

// An event: something that already happened. Past tense.
final class PostSaved
{
    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}

// A listener: what happens because of the event.
final class NotifySubscribers
{
    public function handle(PostSaved $event): void
    {
        echo "Emailing subscribers: new post \"{$event->post->title}\"\n";
    }
}

// An action: what someone asked for. A verb.
final class SavePost
{
    private PostPolicy $policy;
    private PostInput $input;
    private PostsTable $posts;
    private NotifySubscribers $notifySubscribers;

    public function __construct(PostPolicy $policy, PostInput $input, PostsTable $posts, NotifySubscribers $notifySubscribers)
    {
        $this->policy = $policy;
        $this->input = $input;
        $this->posts = $posts;
        $this->notifySubscribers = $notifySubscribers;
    }

    public function handle(array $data, string $user): string
    {
        if (!$this->policy->maySave($user, $data)) {
            return 'Refused: only the author can save this post.';
        }
        $problem = $this->input->problem($data);
        if ($problem !== null) {
            return "Refused: $problem";
        }
        $post = new Post(trim((string) $data['title']), (string) $data['slug'], (string) $data['author']);
        if (!$this->posts->add($post)) {
            return 'Refused: that slug is taken.';
        }
        $this->notifySubscribers->handle(new PostSaved($post));

        return 'Saved.';
    }
}

$savePost = new SavePost(new PostPolicy(), new PostInput(), new PostsTable(), new NotifySubscribers());
echo $savePost->handle(['title' => 'Hello', 'slug' => 'hello', 'author' => 'alice'], 'alice'), "\n";
echo $savePost->handle(['title' => 'Again', 'slug' => 'hello', 'author' => 'alice'], 'alice'), "\n";
echo $savePost->handle(['title' => ' ', 'slug' => 'blank', 'author' => 'alice'], 'alice'), "\n";
echo $savePost->handle(['title' => 'Sneaky', 'slug' => 'sneaky', 'author' => 'alice'], 'bob'), "\n";
