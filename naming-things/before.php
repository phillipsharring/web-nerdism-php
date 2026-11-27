<?php
declare(strict_types=1);

// It works. Now try to say what Mgr is for, what proc() does, or what X is,
// without reading every line.

const X = 5000;

class Mgr
{
    private array $d;

    public function __construct(array $d)
    {
        $this->d = $d;
    }

    public function proc(): array
    {
        $out = [];
        foreach ($this->d as $o) {
            if ($this->chk($o)) {
                $out[] = $o['id'];
            }
        }
        return $out;
    }

    private function chk(array $o): bool
    {
        $flag = $o['paid'] && !$o['nonUs'];
        return $flag && $o['cents'] >= X;
    }
}

$m = new Mgr([
    ['id' => 'A-1001', 'paid' => true, 'nonUs' => false, 'cents' => 6400],
    ['id' => 'A-1002', 'paid' => false, 'nonUs' => false, 'cents' => 9900],
    ['id' => 'A-1003', 'paid' => true, 'nonUs' => false, 'cents' => 5000],
    ['id' => 'A-1004', 'paid' => true, 'nonUs' => true, 'cents' => 12000],
    ['id' => 'A-1005', 'paid' => true, 'nonUs' => false, 'cents' => 1800],
]);

echo 'Free shipping: ' . implode(', ', $m->proc()) . "\n";
