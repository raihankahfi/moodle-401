<?php
// sync_xp.php
// Sinkronisasi level block_xp sesuai XP Moodle
// Level minimum = 1

require_once(__DIR__ . '/../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

global $DB;

// Ambil semua record block_xp
$records = $DB->get_records('block_xp');

$level_threshold = [
    1 => 0,
    2 => 120,
    3 => 276,
    4 => 479,
    5 => 742,
];

foreach ($records as $record) {
    $xp = max(0, (int)$record->xp); // pastikan XP tidak negatif

    $correct_level = 1; // level minimum 1
    foreach ($level_threshold as $lvl => $min_xp) {
        if ($xp >= $min_xp) {
            $correct_level = $lvl;
        }
    }

    // update lvl jika berbeda
    if ($record->lvl != $correct_level) {
        $record->lvl = $correct_level;
        $DB->update_record('block_xp', $record);

        // opsional: log debug
        error_log("Sync XP: User {$record->userid}, Course {$record->courseid}, XP={$xp}, Level={$correct_level}");
    }
}

echo "Sinkronisasi level block_xp selesai.\n";
