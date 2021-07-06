<?php
/* settings/database.php */

return array(
    'mysql' => array(
        'dbdriver' => 'mysql',
        'username' => 'root',
        'password' => '',
        'dbname' => 'edms',
        'prefix' => 'edms',
    ),
    'tables' => array(
        'user' => 'user',
        'category' => 'category',
        'language' => 'language',
        'edms' => 'edms',
        'edms_files' => 'edms_files',
        'edms_download' => 'edms_download',
    ),
);
