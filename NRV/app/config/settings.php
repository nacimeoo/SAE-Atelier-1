<?php
declare(strict_types=1);

$conf = parse_ini_file(__DIR__ . '/.env');

return [
    'db.driver'   => $conf['lib.driver'],
    'db.host'     => $conf['lib.host'],
    'db.database' => $conf['lib.database'],
    'db.username' => $conf['lib.username'],
    'db.password' => $conf['lib.password'],
    'db.dsn'      => "{$conf['lib.driver']}:host={$conf['lib.host']};dbname={$conf['lib.database']}"
];