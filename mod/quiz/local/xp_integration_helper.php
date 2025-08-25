<?php
// File: mod/quiz/classes/local/xp_integration_helper.php

namespace mod_quiz\local;

/**
 * Helper class for integrating with block_xp plugin
 */
class xp_integration_helper {
    
    public static function is_available() {
        global $CFG;
        
        if (!is_dir($CFG->dirroot . '/blocks/xp')) {
            return false;
        }
        
        if (get_config('block_xp', 'enabled') === '0') {
            return false;
        }
        
        return true;
    }
    
    public static function get_user_level($userid, $courseid) {
        global $DB;
        
        if (!self::is_available()) {
            return 1;
        }
        
        try {
            if (class_exists('\block_xp\local\xp\state_store')) {
                return self::get_level_via_api($userid, $courseid);
            }
            
            return self::get_level_via_database($userid, $courseid);
            
        } catch (\Exception $e) {
            debugging('XP Integration Error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return 1;
        }
    }
    
    private static function get_level_via_api($userid, $courseid) {
        try {
            $store = \block_xp\local\xp\state_store::load($courseid);
            $state = $store->get_state($userid);
            
            if ($state && $state->get_level()) {
                return $state->get_level()->get_level();
            }
        } catch (\Exception $e) {
            return self::get_level_via_database($userid, $courseid);
        }
        
        return 1;
    }
    
    private static function get_level_via_database($userid, $courseid) {
        global $DB;
        
        $user_xp = $DB->get_field('block_xp', 'xp', [
            'userid' => $userid,
            'courseid' => $courseid
        ]);
        
        if (!$user_xp) {
            return 1;
        }
        
        $sql = "SELECT level 
                FROM {block_xp_levels} 
                WHERE courseid = ? 
                AND xprequired <= ? 
                ORDER BY level DESC 
                LIMIT 1";
        
        $level = $DB->get_field_sql($sql, [$courseid, $user_xp]);
        
        return $level ? (int)$level : 1;
    }
    
    public static function apply_level_bonus($original_grade, $user_level, $max_grade = null) {
        $result = [
            'grade' => $original_grade,
            'bonus_applied' => false,
            'bonus_percentage' => 0
        ];
        
        if ($user_level == 3) {
            $bonus_grade = $original_grade * 1.1;
            
            if ($max_grade !== null && $bonus_grade > $max_grade) {
                $bonus_grade = $max_grade;
            }
            
            $result['grade'] = $bonus_grade;
            $result['bonus_applied'] = true;
            $result['bonus_percentage'] = 10;
        }
        
        return $result;
    }
}