<?php
/**
 * @package     Vangelis.Tests
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
@ini_set('zend.ze1_compatibility_mode', '0');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overriden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('JPATH_TESTS'))
{
	define('JPATH_TESTS', realpath(__DIR__));
}

// Import the platform.
require_once __DIR__ . '/../code/bootstrap.php';

// Register the core Joomla test classes.
JLoader::registerPrefix('Test', JPATH_PLATFORM . '/../tests/core');
