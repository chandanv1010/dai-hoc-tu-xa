<?php
$base = __DIR__ . '/../app';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base . '/Http/Controllers'));
$errors = [];
foreach ($it as $f) {
    if (!$f->isFile()) continue;
    if (substr($f->getFilename(), -4) !== '.php') continue;
    $content = file_get_contents($f->getPathname());
    if (preg_match_all('/use\s+App\\\\Http\\\\Requests\\\\([A-Za-z0-9_\\\\]+);/m', $content, $m)) {
        foreach ($m[1] as $imp) {
            $candidate = $base . '/Http/Requests/' . str_replace('\\\\', '/', $imp) . '.php';
            if (!file_exists($candidate)) {
                $errors[$f->getPathname()][] = $imp;
            }
        }
    }
}
foreach ($errors as $file => $missing) {
    echo $file . " -> Missing: " . implode(', ', $missing) . PHP_EOL;
}
if (empty($errors)) echo "All request imports resolved.\n";
