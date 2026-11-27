<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Mailer;
use App\ScreenMailer;
use App\Welcome;
use DI\ContainerBuilder;

use function DI\autowire;

// A container does the wiring. Tell it the one thing it can't work out:
// which class to use when something asks for a Mailer.
$builder = new ContainerBuilder();
$builder->addDefinitions([
    Mailer::class => autowire(ScreenMailer::class),
]);
$container = $builder->build();

// It reads Welcome's constructor, sees it needs a Mailer, makes one, and hands it over.
$welcome = $container->get(Welcome::class);
$welcome->send('katherine@example.com', 'Katherine');
