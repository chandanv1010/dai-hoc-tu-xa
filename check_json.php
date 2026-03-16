<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$schoolId = 19;
$langs = DB::table('school_language')->where('school_id', $schoolId)->get();
foreach ($langs as $l) {
    echo "Lang " . $l->language_id . " JSON fields:\n";
    echo "  - majors: " . $l->majors . "\n";
    echo "  - advantage: " . (strlen($l->advantage) > 100 ? substr($l->advantage, 0, 100) . "..." : $l->advantage) . "\n";
}
