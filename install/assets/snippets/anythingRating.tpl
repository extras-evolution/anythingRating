<?php
/**
 * anythingRating
 *
 * Ajax Dynamic Star Rating
 *
 * @category  snippet
 * @version   1.0.1
 * @license   http://creativecommons.org/licenses/by-nc-sa/2.5/ca/ Creative Commons
 * @internal  @properties
 * @internal  @modx_category Content
 * @internal  @installset base, sample
 * @author    Coroico (http://www.modx.wangba.fr)
 *
 * Based on the the code of Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)
 */

global $modx;

// anythingRating version being executed
define('ATR_VERSION', '1.0.1');

// Path where anythingRating is installed
define('ATR_SPATH', 'assets/snippets/anythingrating/');

//==============================================================================
//include snippet file
define ('ATR_PATH', MODX_BASE_PATH . ATR_SPATH);

$output = "";
include(ATR_PATH.'includes/snippet.anythingRating.inc.php');

//Ouput Results
return $output;