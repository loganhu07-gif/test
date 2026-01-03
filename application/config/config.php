<?php
return [
    'admin_roles' => [
        'super' => ['manage_users' => true, 'manage_backups' => true, 'view_stats' => true],
        'editor' => ['manage_users' => false, 'manage_backups' => false, 'view_stats' => true],
    ],
    'salt_length' => 16,
    'max_upload_size' => 2 * 1024 * 1024, // 2MB
    'upload_allowed_types' => ['image/jpeg', 'image/png', 'image/gif'],
];
