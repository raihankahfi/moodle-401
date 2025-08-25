<?php
require_once('../../config.php');
global $CFG;

// Import class di paling atas (sebelum echo/logic lain)
use mod\quiz\local\xp_integration_helper;

echo "Debug Info:<br>";
echo "Moodle root: " . $CFG->dirroot . "<br>";
echo "Looking for file: " . $CFG->dirroot . 'C:\xampp\htdocs\moodle-401\mod\quiz\local\xp_integration_helper.php<br>';

$helper_file = $CFG->dirroot . 'C:\xampp\htdocs\moodle-401\mod\quiz\local\xp_integration_helper.php';
if (file_exists($helper_file)) {
    echo "✓ Helper file found<br>";
    require_once($helper_file);
    
    echo "Testing XP Integration...<br>";
    
    if (xp_integration_helper::is_available()) {
        echo "✓ Block XP is available<br>";
    } else {
        echo "✗ Block XP is not available<br>";
    }
    
    $result = xp_integration_helper::apply_level_bonus(80, 3, 100);
    echo "Level 3 user with grade 80: " . $result['grade'] . " (bonus: " . ($result['bonus_applied'] ? 'Yes' : 'No') . ")<br>";
    
    echo "Test completed!";
} else {
    echo "✗ Helper file NOT found at: " . $helper_file . "<br>";
    echo "Please check if file exists and path is correct.<br>";
}
