<?php
function sanitize_array(array $data): array
{
    $out = [];
    foreach ($data as $k => $v) {
        if (is_string($v)) {
            $out[$k] = trim($v);
        } else {
            $out[$k] = $v;
        }
    }
    return $out;
}

function csrf_field(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}
