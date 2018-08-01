<?php
/**
 * Helper function
 * @author Deepak Pandey <er.pandeydip@gmail.com>
 */

if (!function_exists('dd')) {

    /**
     * Dump and die helper the execution.
     *
     * @author Deepak Pandey
     * @param mixed ...$params
     */
    function dd(...$params)
    {
        dump(...$params);
        die(1);
    }
}
