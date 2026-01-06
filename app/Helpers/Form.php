<?php

namespace App\Helpers;

class Form
{
    public static function open($options = [])
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $action = $options['url'] ?? '';
        $attr = self::attributes($options);

        return "<form action=\"$action\" method=\"$method\" $attr>";
    }

    public static function close()
    {
        return '</form>';
    }

    public static function text($name, $value = '', $options = [])
    {
        return self::input('text', $name, $value, $options);
    }

    public static function number($name, $value = '', $options = [])
    {
        return self::input('number', $name, $value, $options);
    }

    public static function email($name, $value = '', $options = [])
    {
        return self::input('email', $name, $value, $options);
    }

    public static function password($name, $options = [])
    {
        return self::input('password', $name, '', $options);
    }

    public static function hidden($name, $value = '', $options = [])
    {
        return self::input('hidden', $name, $value, $options);
    }

    public static function date($name, $value = '', $options = [])
    {
        return self::input('date', $name, $value, $options);
    }

    public static function file($name, $options = [])
    {
        return self::input('file', $name, '', $options);
    }

    public static function textarea($name, $value = '', $options = [])
    {
        $attr = self::attributes($options);
        return "<textarea name=\"$name\" $attr>$value</textarea>";
    }

    public static function select($name, $list = [], $selected = null, $options = [])
    {
        $attr = self::attributes($options);

        $html = "<select name=\"$name\" $attr>";

        if ($selected instanceof \Illuminate\Support\Collection) {
    $selected = $selected->first() ?? null;
}

foreach ($list as $value => $label) {
    $isSelected = ($selected == $value) ? 'selected' : '';
    $html .= "<option value=\"$value\" $isSelected>$label</option>";
}

        $html .= "</select>";

        return $html;
    }

    private static function input($type, $name, $value, $options)
    {
        $attr = self::attributes($options);
        return "<input type=\"$type\" name=\"$name\" value=\"$value\" $attr>";
    }

    private static function attributes($options)
    {
        unset($options['url'], $options['method']); // remove form params

        $html = '';

foreach ($options as $key => $value) {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $html .= $key . '-' . $k . '="' . e($v) . '" ';
        }
    } else {
        $html .= $key . '="' . e($value) . '" ';
    }
}

return trim($html);

    }
}
