<?php
/* -------------------------------------------------------------
:: Snippet: anythingRating
----------------------------------------------------------------
  Short Description: 
      Rates what you want with anythingRating

  Version:
      1.0

  Created by:
      Coroico (http://www.modx.wangba.fr)
      
  Originally based on:
  
  Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)

  Copyright & Licencing:
  ----------------------
  Licensed under Creative Commons - http://creativecommons.org/licenses/by-nc-sa/2.5/ca/


----------------------------------------------------------------
:: Description
----------------------------------------------------------------

    The anythingRating snippet allow you to rates what you want.
    An ajax rating widget is displayed to get the user rates

MORE : See the anythingRating.readme.txt file for more informations
   
---------------------------------------------------------------- */

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

?>