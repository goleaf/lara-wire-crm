<?php

return [
    'name' => 'Files',
    'storage_directory' => 'crm-files',
    'max_size_kb' => 10240,
    'allowed_extensions' => [
        'pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp',
        'doc', 'docx', 'xls', 'xlsx', 'csv',
        'txt', 'zip', 'rar',
    ],
    'delete_from_disk_on_soft_delete' => false,
];
