<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_quiz\event\attempt_submitted',
        'callback'    => 'local_sync_xp\observer::sync_xp_level',
        'includefile' => '/local/sync_xp/classes/observer.php',
        'priority'    => 1000,
        'internal'    => false,
    ],
    [
        'eventname'   => '\mod_assign\event\assessable_submitted',
        'callback'    => 'local_sync_xp\observer::sync_xp_level',
        'includefile' => '/local/sync_xp/classes/observer.php',
        'priority'    => 1000,
        'internal'    => false,
    ],
    [
        'eventname'   => '\mod_forum\event\post_created',
        'callback'    => 'local_sync_xp\observer::sync_xp_level',
        'includefile' => '/local/sync_xp/classes/observer.php',
        'priority'    => 1000,
        'internal'    => false,
    ],
];
