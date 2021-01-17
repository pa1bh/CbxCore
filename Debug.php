<?php


namespace cybox\cbxcore;

/**
 * Class Debug
 * @package cybox\cbxcore
 */
class Debug
{
    /**
     * Toon de inhoud van een variable op een human readable manier
     *
     * Indien string: string;
     * Indien array of object, grafische reprentatie van die array/object;
     * Indien boolean; TRUE of FALSE
     * Print ook de regelnummer vanwaar de functie opgeroepen wordt, zodat je het nooit kwijtraakt.
     * (Opvolger van debug_dump)
     *
     * @return null|bool
     */
    public static function dump(): ?bool
    {
        $backtrace = debug_backtrace();
        $calling_line = $backtrace['0']['line'];
        $calling_file = preg_replace('@' . $_SERVER['DOCUMENT_ROOT'] . '@', '', $backtrace['0']['file']);
        echo "\n<pre>\n";
        echo '[' . $calling_file . ':' . $calling_line . "]\n";

        $vars = \func_get_args();

        foreach ($vars as $i => $var) {
            echo '' . \gettype($var) . ":\n";
            if (\is_bool($var)) {
                if ($var) {
                    print_r('TRUE');
                } else {
                    print_r('FALSE');
                }
            } else {
                print_r($var);
            }
            echo "\n";
        }

        echo "</pre>\n";
        return true;
    }
}