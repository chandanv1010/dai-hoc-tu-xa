<?php
$dir = __DIR__ . '/../app/Http/Controllers';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$results = ['app_leading' => [], 'facades' => []];
foreach($it as $f) {
    if (!$f->isFile()) continue;
    $path = $f->getPathname();
    if (substr($path, -4) !== '.php') continue;
    $s = file_get_contents($path);
    // find leading \App\ usages
    if (preg_match_all('/\\\\App\\\\[A-Za-z0-9_\\\\]+/', $s, $m)) {
        foreach ($m[0] as $hit) $results['app_leading'][$path][] = $hit;
    }
    // check for Cart usage without use Cart;
    if (preg_match('/\bCart::|\bCart->/', $s)) {
        if (!preg_match('/^use\s+Cart;$/m', $s)) {
            $results['facades'][$path][] = 'Cart';
        }
    }
    // check for ReviewNested or Nestedsetbie usage without use
    foreach (['ReviewNested','Nestedsetbie'] as $cls) {
        if (preg_match('/\b'.$cls.'\b/', $s)) {
            if (!preg_match('/^use\s+.*\\'.$cls.';$/m', $s) && !preg_match('/^use\s+'.$cls.';$/m', $s)) {
                $results['facades'][$path][] = $cls;
            }
        }
    }
}
foreach($results as $k=>$v){
    if (count($v)===0) continue;
    echo "== $k ==\n";
    foreach($v as $p=>$arr) {
        echo $p . " -> " . implode(', ', $arr) . PHP_EOL;
    }
}
