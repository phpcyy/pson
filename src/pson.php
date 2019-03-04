<?php

namespace Pson;

const code_wrong_argv_count = 1;
const code_file_not_exist = 2;

/**
 * @throws \Exception
 */
function get_input()
{
    if (ftell(STDIN) === 0) {
        return stream_get_contents(STDIN);
    } else if (count($GLOBALS['argv']) > 1) {
        $file = $GLOBALS['argv'][1];
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        throw new \Exception("", code_file_not_exist);
    } else {
        throw new \Exception("", code_wrong_argv_count);
    }
}

function usage()
{
    return <<<EOF
Usage:
    pson [files...]

Example:
    pson config.json
    echo '{\"message\": \"hello, 世界\"}' | pson
EOF;
}

function output($out, $tab, $newline, $comma = false)
{
    $format = "";
    if (!is_array($out)) {
        $format .= output_var($out, $newline ? $tab : 0, 1);
    } else {
        if (is_assoc($out)) {
            $format .= echo_tab("\033[30m{\n\033[0m", $newline ? $tab : 0);

            foreach ($out as $k => $item) {
                $format .= output_var($k, $tab + 1, false, true) . ": " . output($item, $tab + 1, false, $k != array_keys($out)[count($out) - 1]);
            }

            $format .= echo_tab("\e[30m}\e[0m\n", $tab);
        } else {
            $format .= echo_tab("\e[37m[\e[0m\n", $newline ? $tab : 0);
            if (empty($out)) {
                $format .= "\n";
            }
            foreach ($out as $k => $item) {
                $format .= output($item, $tab + 1, true, $k != count($out) - 1);
            }
            $format .= echo_tab("\e[37m]\e[0m\n", $tab);
        }
    }

    return $comma ? rtrim($format, "\n") . ",\n" : $format;
}

function is_assoc($arr)
{
    return array_values($arr) !== $arr;
}

function output_var($out, $tab, $appendLine, $is_key = false)
{
    $format = "";
    if (is_float($out) || is_int($out)) {
        $format = "\e[31m" . $out . "\e[0m";
    }

    if (is_string($out)) {
        if ($is_key) {
            $format = "\e[36m" . sprintf('"%s"', $out) . "\e[0m";
        } else {
            $format = "\e[35m" . sprintf('"%s"', $out) . "\e[0m";
        }
    }

    if (is_bool($out)) {
        $format = "\e[33m" . sprintf('"%s"', $out) . "\e[0m";
    }

    if (is_null($out)) {
        $format = "\e[34mnull\e[0m";
    }

    $format = echo_tab($format, $tab);

    if ($appendLine) $format .= "\n";
    return $format;
}

function echo_tab($str, $tab)
{
    return str_repeat("    ", $tab) . $str;
}


