//<?php
/**
 * anythingRating
 * 
 * Ajax Dynamic Star Rating
 *
 * @category  snippet
 * @version   1.0
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal  @properties
 * @internal  @modx_category Content
 * @internal    @installset base, sample
 */

global $modx;
// anythingRating version being executed
define('ATR_VERSION', '1.0');
// Path where anythingRating is installed
define('ATR_SPATH', 'assets/snippets/anythingRating/');
//==============================================================================
//include snippet file
define ('ATR_PATH', $modx->config['base_path'].ATR_SPATH);

$output = "";
include(ATR_PATH.'includes/snippet.anythingRating.inc.php');

//Ouput Results
return $output;
