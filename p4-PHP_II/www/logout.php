<?php

require_once __DIR__ . '/config/twig.php';

$_SESSION = [];
session_destroy();
header('Location: portada.php');
exit;
