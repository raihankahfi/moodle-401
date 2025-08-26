<?php
namespace local_sync_xp;

defined('MOODLE_INTERNAL') || die();

class observer {

    /**
     * Observer otomatis, dipanggil saat event Moodle terjadi
     */
    public static function sync_xp_level($event) {
        global $DB;

        $userid   = $event->relateduserid;
        $courseid = $event->courseid ?? 0;

        if ($record = $DB->get_record('block_xp', ['userid' => $userid, 'courseid' => $courseid])) {
            // Hitung level sesuai XP Moodle
            $xp = $record->xp;
            $level = 1;
            if ($xp >= 742) $level = 5;
            else if ($xp >= 479) $level = 4;
            else if ($xp >= 276) $level = 3;
            else if ($xp >= 120) $level = 2;

            $record->lvl = $level;
            $DB->update_record('block_xp', $record);
        }
    }

    /**
     * Sinkronisasi level manual (dipanggil di process_finish)
     */
    public static function sync_xp_level_manual($userid, $courseid) {
        global $DB;

        if ($record = $DB->get_record('block_xp', ['userid' => $userid, 'courseid' => $courseid])) {
            $xp = $record->xp;
            $level = 1;
            if ($xp >= 742) $level = 5;
            else if ($xp >= 479) $level = 4;
            else if ($xp >= 276) $level = 3;
            else if ($xp >= 120) $level = 2;

            $record->lvl = $level;
            $DB->update_record('block_xp', $record);
        }
    }
}
