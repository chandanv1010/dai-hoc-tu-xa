<?php
$dir = __DIR__ . '/../app/Http/Controllers';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$results = [];
foreach($it as $f) {
    if (!$f->isFile()) continue;
    $path = $f->getPathname();
    if (!preg_match('#\\\\app\\\\Http\\\\Controllers\\\\(Ajax|Backend|Frontend)#', $path)) continue;
    if (substr($path, -4) !== '.php') continue;
    $s = file_get_contents($path);
    preg_match_all('/\\$this->([A-Za-z0-9_]+)\b/', $s, $m);
    $props_used = array_unique($m[1]);
    preg_match_all('/(protected|public|private)\\s+\\$([A-Za-z0-9_]+)\\b/', $s, $d);
    $declared = array_unique($d[2]);
    $missing = [];
    foreach ($props_used as $p) {
        if ($p === '') continue;
        if (!in_array($p, $declared)) $missing[] = $p;
    }
    if (count($missing)) {
        $results[$path] = $missing;
    }
}
foreach($results as $path => $missing){
    echo $path . " :: " . implode(', ', $missing) . PHP_EOL;
}
