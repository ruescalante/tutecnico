<?php
require_once BASE_PATH . '/exceptions/ValidationException.php';

class Validator
{
    public static function validate(array $data, array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $ruleStr) {
            $ruleset = explode('|', $ruleStr);
            $value = $data[$field] ?? null;

            foreach ($ruleset as $rule) {
                $param = null;
                if (strpos($rule, ':') !== false) {
                    [$rname, $param] = explode(':', $rule, 2);
                } else {
                    $rname = $rule;
                }

                switch ($rname) {
                    case 'required':
                        if ($value === null || $value === '') {
                            $errors[$field][] = 'El campo es obligatorio';
                        }
                        break;
                    case 'max':
                        if ($value !== null && mb_strlen((string)$value) > (int)$param) {
                            $errors[$field][] = "Máximo {$param} caracteres";
                        }
                        break;
                    case 'min':
                        if ($value !== null && mb_strlen((string)$value) < (int)$param) {
                            $errors[$field][] = "Mínimo {$param} caracteres";
                        }
                        break;
                    case 'integer':
                        if ($value !== null && !filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field][] = 'Debe ser un entero';
                        }
                        break;
                    case 'email':
                        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Email inválido';
                        }
                        break;
                    case 'in':
                        $opts = explode(',', $param);
                        if ($value !== null && !in_array($value, $opts)) {
                            $errors[$field][] = 'Valor inválido';
                        }
                        break;
                    case 'regex':
                        if ($value !== null && !preg_match($param, (string)$value)) {
                            $errors[$field][] = 'Formato inválido';
                        }
                        break;
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
