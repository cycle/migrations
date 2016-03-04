<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

/**
 * Error reporting.
 */
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

mb_internal_encoding('UTF-8');

//Composer
require dirname(__DIR__) . '/vendor/autoload.php';