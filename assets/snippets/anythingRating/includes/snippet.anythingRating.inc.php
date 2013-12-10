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

  Partly based on:

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

define('ATR_IVERSION', "1.0.1");

// check version files
if (!defined ('ATR_VERSION')){
  $output = '<h3>anythingRating version is not defined. <br />Please check the snippet code in MODx manager.</h3>'; 
  return;
}
elseif (ATR_VERSION != ATR_IVERSION) {
  $output = '<h3>anythingRating version is obsolete. <br />Please check the snippet code in MODx manager.</h3>';
  return;
}
elseif (!defined ('ATR_PATH')){
  $output = 'anythingRating setup path is not defined. <br />Please check the snippet code in MODx manager.</h3>'; 
  return;
}

//-------------------------------------------------------------------------------------------------
// CONFIGURE - GENERAL SNIPPET SETUP OPTIONS
//-------------------------------------------------------------------------------------------------

$cfg = array(
    // full path to anythingRating directory
    'base' => ATR_SPATH,

    // anythingRating version
    'version' => ATR_VERSION,

    // Unique rating group name
    // Any combination of characters a-z, underscores, and numbers 0-9
    'atrGrp' => isset($atrGrp) ? $atrGrp : 'ratings',

    // Define the rating group
    'define' => isset($define) ? $define : 0,

    // Get the top rated item list
    'getTopRated' => isset($getTopRated) ? $getTopRated : 0,

	// build XHTML compatible URLs [ 1 | 0 ]
    'xhtmlUrl' => isset($xhtmlUrl) ? (int) $xhtmlUrl : 1,

	// friendly URLs
	'friendlyUrls' => $modx->config['friendly_urls']
);
      
if ($cfg['define']){

// Here are the parameters which will be applied for the rating roup

    // language [ language_name | manager_language ] (optional)
    // with manager_language = $modx->config['manager_language'] by default 
    $cfg['language'] = isset($language) ? $language : $modx->config['manager_language'];

    // number of stars  [positive integer | 5 ]
    $cfg['nbStars'] = isset($nbStars) ? $nbStars : 5;    

    // Number of IP adresses stored [positive integer | 'all']
    // 'all' to store all IP addresses 
    $cfg['nbIP'] = isset($nbIP) ? $nbIP : 'all';

    // End date to vote [ date | 'unlimited']
    // 'unlimited' for an unlimited end date
    $cfg['endDate'] = isset($endDate) ? $endDate : 'unlimited';

    // List of web user group [ webGroupList | 'all' ]
    // Comma delimited list of web user group
    // 'all' for a public access
    $cfg['canVote'] = isset($canVote) ? $canVote : 'all';    

    // Allow multiple vote [ 1 | 0 ]
    $cfg['multiVote'] = isset($multiVote) ? $multiVote : 0;    

    // css file to style the rating widget 
    $cfg['atrCss'] = isset($atrCss) ? $atrCss : ATR_SPATH.'css/anythingRating.css';

    // tpl file to style the rating widget
    // path to the template file
    $cfg['atrTpl'] = isset($atrTpl) ? $atrTpl : "@FILE:".ATR_SPATH.'templates/anythingRating.tpl.html';

}
else if ($cfg['getTopRated']){

// Here are the parameters which are linked with the get top rated option

    // top number [positive integer | 5 ]
    $cfg['topNb'] = isset($topNb) ? $topNb : 5;

    // top Direction [ 'worst' | 'best' ]
    $cfg['topDir'] = isset($topDir) ? $topDir : 'best';

    // tpl file to style the output display
    // path to the template file
    $cfg['topTpl'] = isset($topTpl) ? $topTpl : "@FILE:".ATR_SPATH.'templates/topRated.tpl.html';

    // label of the top rated item  [ label | 'rated item']
    $cfg['topLabel'] = isset($topLabel) ? $topLabel : 'items';

    // table name of the rated items  [name of an existing table ]
    $cfg['topTable'] = isset($topTable) ? $topTable : '';

    // id field of the rated items  [id field name | 'id']
    // 'id' by default
    $cfg['topIdField'] = isset($topIdField) ? $topIdField : 'id';

    // title field of the rated items  [title field name | 'title']
    // 'title' by default
    $cfg['topTitleField'] = isset($topTitleField) ? $topTitleField : 'title';

    // description field of the rated items  [description field name] (optional)
    $cfg['topDescrField'] = isset($topDescrField) ? $topDescrField : '';

    // image field of the rated items  [image field name] (optional)
    $cfg['topImageField'] = isset($topImageField) ? $topImageField : '';

    // link field of the rated items  [link field name] (optional)
    $cfg['topLinkField'] = isset($topLinkField) ? $topLinkField : '';

}
else {

// here are the parameters which are specific to a rated item

    // Unique id for this anythingRating instance
    // Any combination of characters a-z, underscores, and numbers 0-9
    $cfg['atrId'] = isset($atrId) ? $atrId : 0;

    // No votes  [1 | 0]
    // forbid vote for a specific atrId
    $cfg['noVotes'] = isset($noVotes) ? $noVotes : 0;

    // Initialize the vote with a rating value or from content of a TV
    $cfg['init'] = isset($init) ? $init : 0;
}

// End configure

//-------------------------------------------------------------------------------------------------
//  SNIPPET LOGIC CODE STARTS HERE
//-------------------------------------------------------------------------------------------------

include_once ATR_PATH."classes/anythingRating.class.inc.php";

if (class_exists('AnythingRating')) {
  $atr = new AnythingRating($cfg);
} else {
  $output = "<h3>error: AnythingRating class not found</h3>";
  return;
}

//Process anythingRating
$output = $atr->run();

?>