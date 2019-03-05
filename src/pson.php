<?php

namespace Pson;

const code_wrong_argv_count = 1;

/**
 * Main function.
 */
function bin()
{
    try {
        foreach (get_inputs() as $index => $input) {
            if ($index > 0) {
                echo "\n";
            }
            if (is_resource($input)) {
                $content = stream_get_contents($input);
            } else {
                if (!file_exists($input)) {
                    echo sprintf("File %s is not exist.", $input);
                    continue;
                }
                $content = file_get_contents($input);
            }
            $json = json_decode($content, true);
            switch (json_last_error()) {
                case 0:
                    echo output($json, 0, true);
                    break;
                case JSON_ERROR_SYNTAX:
                    echo sprintf("The input is not a valid json.");
                    break;
                default:
                    echo json_last_error_msg();
            }
        }
    } catch (\Exception $exception) {
        if ($exception->getCode() == code_wrong_argv_count) {
            echo usage();
        } else {
            echo $exception->getMessage();
        }
    }
}

/**
 * Get JSON from STDIN or file.
 *
 * @throws \Exception
 */
function get_inputs()
{
    if (ftell(STDIN) === 0) {
        return [STDIN];
    } else if (count($GLOBALS['argv']) > 1) {
        return array_slice($GLOBALS['argv'], 1);
    }
    throw new \Exception("", code_wrong_argv_count);
}

/**
 * Usage of Pson.
 *
 * @return string
 */
function usage()
{
    return <<<EOF
Usage:
    pson [files...]

Example:
    pson config.json
    echo "{\"message\": \"hello, 世界\"}" | pson
EOF;
}

/**
 * Simply transfer PHP array into pretty colored json.
 *
 * @param $out
 * @param $tab
 * @param $newline
 * @param bool $comma
 * @return string
 */
function output($out, $tab, $newline, $comma = false)
{
    $format = "";
    if (!is_array($out)) {
        $format .= output_var($out, $newline ? $tab : 0, 1);
    } else {
        if (is_assoc($out)) {
            $format .= echo_tab(green("{") . "\n", $newline ? $tab : 0);

            foreach ($out as $k => $item) {
                $format .= output_var($k, $tab + 1, false, true) . ": " . output($item, $tab + 1, false, $k != array_keys($out)[count($out) - 1]);
            }

            $format .= echo_tab(green("}") . "\n", $tab);
        } else {
            $format .= echo_tab(white("[") . "\n", $newline ? $tab : 0);
            if (empty($out)) {
                $format .= "\n";
            }
            foreach ($out as $k => $item) {
                $format .= output($item, $tab + 1, true, $k != count($out) - 1);
            }
            $format .= echo_tab(white("]") . "\n", $tab);
        }
    }

    return $comma ? rtrim($format, "\n") . ",\n" : $format;
}

/**
 * Decide the array is a key-value array or a pure array.
 *
 * @param $arr
 * @return bool
 */
function is_assoc($arr)
{
    return array_values($arr) !== $arr;
}

/**
 * Format the output.
 *
 * @param $out
 * @param $tab
 * @param $appendLine
 * @param bool $is_key
 * @return string
 */
function output_var($out, $tab, $appendLine, $is_key = false)
{
    $format = "";
    if (is_float($out) || is_int($out)) {
        $format = blue($out);
    }

    if (is_string($out)) {
        if ($is_key) {
            $format = darkGreen(sprintf('"%s"', addslashes($out)));
        } else {
            $format = purple(sprintf('"%s"', addslashes($out)));
        }
    }

    if (is_bool($out)) {
        $format = yellow($out ? "true" : "false");
    }

    if (is_null($out)) {
        $format = red("null");
    }

    $format = echo_tab($format, $tab);

    if ($appendLine) $format .= "\n";
    return $format;
}

/**
 * Get a string with specific num of pre tabs.
 *
 * @param $str
 * @param $tab
 * @return string
 */
function echo_tab($str, $tab)
{
    return str_repeat("    ", $tab) . $str;
}

/***
 * The following code are functions to output with different colors.
 */


/**
 * @param $content
 * @return string
 */
function black($content)
{
    return sprintf("\e[30m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function red($content)
{
    return sprintf("\e[31m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function green($content)
{
    return sprintf("\e[32m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function yellow($content)
{
    return sprintf("\e[33m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function blue($content)
{
    return sprintf("\e[34m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function purple($content)
{
    return sprintf("\e[35m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function darkGreen($content)
{
    return sprintf("\e[36m%s\e[0m", $content);
}

/**
 * @param $content
 * @return string
 */
function white($content)
{
    return sprintf("\e[37m%s\e[0m", $content);
}