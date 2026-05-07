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
