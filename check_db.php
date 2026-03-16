<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\School;
use App\Models\Major;
use Illuminate\Support\Facades\DB;

$schoolId = 19;
$school = School::find($schoolId);

if (!$school) {
    echo "School $schoolId not found\n";
} else {
    echo "School $schoolId found: " . $school->short_name . " (Publish: " . $school->publish . ")\n";
    $langs = DB::table('school_language')->where('school_id', $schoolId)->get();
    foreach ($langs as $l) {
        echo "  - Lang " . $l->language_id . " name: " . $l->name . "\n";
    }
}

echo "\nChecking major 'VIỆT NAM HỌC'...\n";
$major = Major::whereHas('languages', function($q) {
    $q->where('major_language.name', 'LIKE', '%VIỆT NAM HỌC%');
})->first();

if (!$major) {
    echo "Major 'VIỆT NAM HỌC' not found\n";
} else {
    echo "Major '" . $major->languages->first()->pivot->name . "' (ID: " . $major->id . ") found\n";
    $schools = $major->schools;
    echo "Related schools count: " . $schools->count() . "\n";
    foreach ($schools as $s) {
        echo "  - School ID: " . $s->id . ", Short Name: " . $s->short_name . ", Publish: " . $s->publish . "\n";
    }
}

echo "\nChecking school_major table directly for School $schoolId...\n";
$relations = DB::table('school_major')->where('school_id', $schoolId)->get();
foreach ($relations as $r) {
    echo "  - Major ID: " . $r->major_id . "\n";
}
