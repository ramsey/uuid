<?php

namespace Rhumsaa\Uuid;

/**
 * Overriding the default php_uname() for the purpose of this test so we can
 * have full code coverage on all the lines.
 *
 * This should ensure that we have full code coverage whether the tests are run
 * on Windows or a *NIX environment. This does not mean that the code has been
 * tested for both environments if it is only tested in one environment, so
 * the tests must still be run in both environments to ensure they pass.
 */
function php_uname($v)
{
    static $run = 0;

    if ($run == 0 && strtoupper(substr(\php_uname('a'), 0, 3)) != 'WIN') {
        // If this is the first run and the SUT is not Windows, return "Windows"
        $ret = 'Windows';
    } elseif ($run == 0 && strtoupper(substr(\php_uname('a'), 0, 3)) == 'WIN') {
        // If this is the first run and the SUT is Windows, return "Darwin"
        $ret = 'Darwin';
    } else {
        // If this isn't the first run, then use the system php_uname()
        $ret = \php_uname($v);
    }

    $run++;

    return $ret;
}
