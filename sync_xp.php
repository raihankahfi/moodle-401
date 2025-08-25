<?php
require_once(__DIR__ . '/config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

global $DB;

$courseid = 2;

// Pastikan Block XP tersedia
if (!class_exists('\block_xp\di')) {
    die("Block XP library tidak ditemukan.");
}

// Ambil semua record Block XP untuk course ini
$records = $DB->get_records('block_xp', ['courseid' => $courseid]);

// Ambil world object dan levels info
$world = \block_xp\di::get('course_world_factory')->get_world($courseid);
$levelsinfo = $world->get_levels_info();
$levels = $levelsinfo->get_levels();
$maxlevel = max(array_keys($levels));

foreach ($records as $r) {
    $xp = $r->xp;

    // Hitung level dari XP, aman terhadap XP terlalu tinggi
    try {
        $levelobj = $levelsinfo->get_level($xp);
        $correctlevel = $levelobj->get_level_number();
    } catch (\coding_exception $e) {
        // Jika XP di luar range level, set ke level maksimum
        $correctlevel = $maxlevel;
    }

    // Update lvl di DB hanya jika berbeda
    if ($r->lvl != $correctlevel) {
        $DB->update_record('block_xp', [
            'id' => $r->id,
            'lvl' => $correctlevel
        ]);
        echo "UserID {$r->userid} | XP {$xp} | DB Level updated from {$r->lvl} to {$correctlevel}<br>";
    }
}
