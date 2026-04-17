<?php
require_once __DIR__ . '/auth.php';

logoutAdmin();
header('Location: admin.php');
exit;
