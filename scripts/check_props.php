<?php
$dir = __DIR__ . '/../app/Http/Controllers';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach($it as $f) {
    if (!$f->isFile()) continue;
    $path = $f->getPathname();
    if (!preg_match('#\\\\app\\\\Http\\\\Controllers\\\\(Ajax|Backend|Frontend)#', $path)) continue;
    if (substr($path, -4) !== '.php') continue;
    $s = file_get_contents($path);
    preg_match_all('/\\$this->([A-Za-z0-9_]+)\\s*=/', $s, $m);
    $props = array_unique($m[1]);
    $missing = [];
    foreach ($props as $p) {
        if ($p === '') continue;
        if (!preg_match('/(protected|public|private)\\s+\\$' . preg_quote($p, '/') . '\\b/', $s)) $missing[] = $p;
    }
    if (count($missing)) echo $path . " :: " . implode(',', $missing) . PHP_EOL;
}
