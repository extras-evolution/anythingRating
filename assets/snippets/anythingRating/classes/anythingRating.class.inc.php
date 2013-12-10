<?php
/*
 * Title: AnythingRating Class
 * Purpose:
 *    The anythingRating snippet allow you to rates what you want.
 *    An ajax rating widget is displayed to get the user rates
 *    
 *    Version: 1.0  - Coroico (www.modx.wangba.fr) 
 * 
 *    Partly based on Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)
 *    and modified by Coroico (www.modx.wangba.fr)
 *
 *    Licensed under Creative Commons - http://creativecommons.org/licenses/by-nc-sa/2.5/ca/   
*/

class AnythingRating{

  var $config;
  var $tbRating;
  var $template;
  var $allowedToVote;
  var $lang;        

  function AnythingRating($cfg) {

    global $modx;

    // load config snippet options
    $this->config = $cfg;

    $prefix = $modx->db->config['table_prefix']."atRating_";  //The prefix to the ratings table.
    $this->tbRating  = $prefix.$this->config['atrGrp'];       //The name of the ratings table.

    if ($cfg['define']){
      // create or update the rating group

      // adds the anythingRating js file
      $jsf = '<script src="'.$this->config['base'].'js/anythingRating.js" type="text/javascript"></script>';
      $modx->regClientStartupScript($jsf);

      // adds the anythingRating css file
      $modx->regClientCSS($this->config['atrCss']);

      if ($cfg['endDate'] == 'unlimited') $this->config['endDate'] = "2036-12-31"; 
      if ($cfg['nbIP'] == 'all') $this->config['nbIP'] = 1000000;
      $this->config['multiVote'] = $cfg['multiVote'] ? 1 : 0;

      // create or update the rating table
      if (!$this->existTable($modx->db->config['dbase'],$this->tbRating)) {
      // creation of the rating table
          $this->createRatingTable($this->tbRating);
      }
      else {
        // update of the rating table
        $this->updateRatingTable($this->tbRating);
      }
    }
    else {

      // get parameters from the table
      $info = $this->getInfoGroup($this->tbRating);
      $this->config['endDate'] = $info['endDate'];
      $this->config['language'] = $info['language'];
      $this->config['canVote'] = $info['canVote'];
      $this->config['multiVote'] = $info['multiVote'];
      $this->config['nbStars'] = $info['nbStars'];
      $this->config['nbIP'] = $info['nbIP'];
      $this->config['atrTpl'] = $info['atrTpl'];    

      // set the snippet folder path
      $this->config['base'] = strtr(realpath(dirname(__FILE__))."/../", '\\', '/');
    }

    // allowed to vote ?
    if ($this->config['canVote'] == 'all') $this->allowedToVote = true;
    else {
        $webGroup = explode(',',$this->config['canVote']);
        $this->allowedToVote = $modx->isMemberOfWebGroup($webGroup);
    }

    // get the language file
    include($this->config['base']."lang/english.inc.php");
    if ($this->config['language'] != "english" && $this->config['language'] != '') {
        $langfile = $this->config['base']."lang/".$this->config['language'].".inc.php";
        if (file_exists($langfile)) include $langfile;
    }
    $this->lang = $_lang;
  }

  function run(){

    global $modx;

    $grp = $this->config['atrGrp'];   // Id of the group of ratings
    $id = $this->config['atrId'];     // Id of the current rated item

    if (isset($_GET['rating']) && isset($_GET['atrGrp']) && ($grp == $modx->db->escape($_GET['atrGrp'])) 
        && isset($_GET['atrId']) && ($id == $modx->db->escape($_GET['atrId']))){

      // JavaScript disabled, update the rating value of the item

      $rating = (int) $modx->db->escape($_GET['rating']);
      $this->doVote($rating,false);
    }

    if ($this->config['define']) return;
    else if ($this->config['getTopRated']) return $this->getTopRated();
    else return $this->showStars();
  }

  /**
    * anythingRating::getRating
    *
    * Get the current rating from the rating group table
    *
    */
  function getRating() {  
    global $modx;
    $SQL_GET_RATINGS = "SELECT SUM( $this->tbRating.rating ) as totalRating, 
        SUM( $this->tbRating.nbIP ) AS totalNbIP  
        FROM $this->tbRating 
        WHERE $this->tbRating.rating_id = '".$this->config['atrId']."' 
        GROUP BY $this->tbRating.rating_id;";

    $result =  $modx->db->getRow($modx->db->query($SQL_GET_RATINGS));
    if (!$result) {
      $init = $this->config['init'];
      if ($init){
          // init rating value
          if (is_numeric($init)) {
              // from a value
              $rating = (int) $init;
              if (($rating < 1) || ($rating > $this->config['nbStars'])) $rating = 0;
          }
          else {
            // from a TV information
            $info = explode(':', $init);
            $tvName = $info[0];
            $tvDoc = isset($info[1]) ? $info[1] : $modx->documentObject['id'];
            $tvValue = $modx->getTemplateVar($tvName, '*',$tvDoc);
            if ($tvValue){
                $splitVal = explode(':', $tvValue['value']);
                $rating = $splitVal[0];
            }
            else $rating = 0;
          }
          if ($rating) {
              $rating = $rating * 100 / $this->config['nbStars'];
              $this->doVote($rating,false);
              $result = array( 'totalRating' => $rating,'totalNbIP' => 1 );
          } 
          else $result = array( 'totalRating' => 0,'totalNbIP' => 0 );
      }
      else $result = array( 'totalRating' => 0,'totalNbIP' => 0 );
    }

    return $result;
  }

  function percentToStars($percent) {
    $modifier = 100 / $this->config['nbStars'];
    return round($percent / $modifier, 1);
  }

  function getStarMessage($rating, $fullMsg = false) {   
    $avgRating = $rating['totalNbIP'] > 0 ? $rating['totalRating']/$rating['totalNbIP'] : 0;        
    $percent = round($avgRating, 0)."%";
    $stars = $this->percentToStars($avgRating);

    $lbvotes = ($rating['totalNbIP'] == 1) ? $this->lang['atr_lbvote'] : $this->lang['atr_lbvotes'];

    // get score template
    if (!class_exists('atrChunkie')) 
        include_once($this->config['base']."/chunkie/chunkie.class.inc.php");
    $tpl = new atrChunkie($this->config['atrTpl']);
    $this->template = "@CODE:".$tpl->template;

    $atr = array(
      'tpl'=> "score",
      'intro+]'  => $this->lang['atr_intro'],
      'rating'  => $stars,
      'nbstars' => $this->config['nbStars'],
      'lbstars' => $this->lang['atr_lbstars'],
      'percent' => $percent,
      'nbvotes' => $rating['totalNbIP'],
      'lbvotes' => $lbvotes            
    );
    
    $tpl->AddVar("atr",$atr);
    $msg = $tpl->Render();
    $msg = preg_replace("/(\r\n|\n|\r)/", " ", $msg); 

    // thanks for voting msg
    $thanks = $this->lang['atr_thanks_voting'];
    // multiVote allowed ?
    $mVote = ($this->config['multiVote']) ? 'multiVote' : 'oneVote';
    // || used as separator
    if ( $fullMsg ) $msg = "$msg||$percent||$thanks||$mvote";

    return $msg;
  }

  /**
    * anythingRating::showStars
    *
    * display the current rating
    *
    */

  function showStars(){

    $grp = $this->config['atrGrp'];
    $id  = $this->config['atrId'];
    $nbs = $this->config['nbStars'];

    $rating = $this->getRating();
    $avgRating = $rating['totalNbIP'] > 0 ? $rating['totalRating']/$rating['totalNbIP'] : 0;
        
    $stars = $this->percentToStars($avgRating);
    $percent = round($avgRating, 0)."%";

    $onestar_outof = $this->lang['atr_onestar_outof'];
    $stars_outof = $this->lang['atr_stars_outof'];
    $tplLi = '<li><a onclick="[+atr.onclick+]" href="[+atr.href+]" title="[+atr.title+]" class="[+atr.class+]" >[+atr.lnk+]</a></li>'."\n";

    $liSection = '<li class="current-rating" style="width:'.$percent.';"></li>'."\n";
    
    // No vote allowed for this user or item already voted
    if (!$this->allowedToVote || $this->config['noVotes'] || isset($_COOKIE[$grp . '_' . $id])) {

        $raterClass = "star-rating2";
        for ($i = 1; $i <= $nbs; $i++) {

            $label = ($i == 1) ? $onestar_outof : $stars_outof;
            $label = str_replace('%s',$i,$label);
            $label = str_replace('%n',$nbs,$label);

            $liPh = array(
              '[+atr.onclick+]' => 'return false;',
              '[+atr.href+]' => $this->getUrl(),
              '[+atr.title+]' => $label,
              '[+atr.class+]' => 'stars'.$i,
              '[+atr.lnk+]' => $i            
            );
            $textLi = str_replace(array_keys($liPh),array_values($liPh),$tplLi);
            unset($liPh);
            $liSection .= $textLi;
        }
    }
    else {
        // allowed to vote for this item
        $raterClass = "star-rating";    
        for ($i = 1; $i <= $nbs; $i++) {

            $label = ($i==1) ? $onestar_outof : $stars_outof;
            $label = str_replace('%s', $i, $label);
            $label = str_replace('%n', $nbs, $label);

            $curl = array(
				'atrGrp' => $grp,
				'atrId' => $id,
				'rating' => $i * 100 / $nbs
				);

            $liPh = array(
              '[+atr.onclick+]' => 'rate(\'' . $i * 100 / $nbs . '\',\'' . $grp . '\',\'' . $id . '\');return false;',
              '[+atr.href+]' => $this->getUrl($curl),
              '[+atr.title+]' => $label,
              '[+atr.class+]' => 'stars' . $i,
              '[+atr.lnk+]' => $i
            );
            $textLi = str_replace(array_keys($liPh), array_values($liPh), $tplLi);
            unset($liPh);
            $liSection .= $textLi;
        }
    }

    // get rating template  (read file once)  
    $scoreSection = $this->getStarMessage($rating, false);

    if (!class_exists('atrChunkie')) 
        include_once($this->config['base']."/chunkie/chunkie.class.inc.php");
    $tpl = new atrChunkie($this->template);
    $atr = array(
      'tpl'=> "rating",
      'scoreId' => "score_".$id,
      'scoreSection' => $scoreSection,
      'raterClass' => $raterClass,
      'raterId' => "rater_".$id,
      'raterLiSection' => $liSection,
      'msgId' => "msg_".$id,
      'msgSection' => ""
    );

    $tpl->AddVar("atr",$atr);
    $output = $tpl->Render();

    return $output;
  }

  function getInfoGroup($tbName){ 
    global $modx;  
    // reading of header table to get endDate, nbStars, language, nbIP
    $fields = "rating_id as 'params', rating as 'nbStars', lastIP as 'atrTpl', nbIP, voteDate as 'endDate'";
    $where = "id = '1'";
    $info = $modx->db->getRow($modx->db->select($fields,$tbName,$where));
    $p = explode(':',$info['params']);
    unset($info['params']);
    $info['language'] = $p[0];
    $info['canVote'] = $p[1];
    $info['multiVote'] = $p[2];
    return $info;
  }

  function existTable($db,$tbName){
    global $modx;       
    $show = "SHOW TABLES FROM $db LIKE '$tbName';";
    $exec = $modx->db->query($show);
    return $modx->db->getRecordCount($exec);
  }

  function existField($tbName, $fldName){
    global $modx;
    $show = "SHOW COLUMNS FROM $tbName LIKE '$fldName';";
    $exec = $modx->db->query($show);
    return $modx->db->getRecordCount($exec);
  }

  function createRatingTable($tbName){
    global $_lang;
    global $modx;

    // set the table header
    $nbs = $this->config['nbStars'];
    $nbIP = $this->config['nbIP'];
    $language = $this->config['language'];
    $canVote = $this->config['canVote'];
    $multiVote = $this->config['multiVote'];
    $tpl = $this->config['atrTpl'];

    $sdate = explode('-',$this->config['endDate']);
    if (checkdate($sdate[1],$sdate[2],$sdate[0])) {

      $SQL_CREATE_TABLE = "CREATE TABLE $tbName (
          `id` int(11) NOT NULL auto_increment,
          `rating_id` VARCHAR(80) NOT NULL,
          `rating` int(11) NOT NULL,
          `lastIP` varchar(255) NOT NULL,
          `nbIP` int(11) NOT NULL,
          `voteDate` TIMESTAMP,
          PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM;";

      if  (!$modx->db->query($SQL_CREATE_TABLE)){
          return false;
      }
      else {
          // storage of the end date as a time stamp in the first row
          $idate = $sdate[0].'-'.$sdate[1].'-'.$sdate[2];
          $params = "$language:$canVote:$multiVote";
          $INSERT_HEADER = "INSERT INTO ".$tbName." (rating_id, rating, lastIP, nbIP, voteDate) 
              VALUES ('$params','$nbs','$tpl','$nbIP','$idate')";
          $modx->db->query($INSERT_HEADER);

          return true;
        }
      }
      else {
          echo("Your endDate parameter: ".$this->config['endDate']." is wrong!");
          exit;
      }
  }

  /**
    * anythingRating::updateRatingTable
    *
    * update parameters of the rating group
    *
    * nbStars can't change
    * nbIP and endDate could increase only
    * language, canVote, multiVote and tpl could be changed at any time   
    */

  function updateRatingTable($tbName){
    global $modx;
    $info = $this->getInfoGroup($tbName);

    $endDate = $this->config['endDate'];
    $nbIP = $this->config['nbIP'];
    $language = $this->config['language'];
    $canVote = $this->config['canVote'];
    $multiVote = $this->config['multiVote'];
    $tpl = $this->config['atrTpl'];

    if ( strtotime($endDate) > strtotime($info['voteDate']) ) $fields['voteDate'] = $endDate;
    if ( $nbIP > $info['nbIP'] ) $fields['nbIP'] = $nbIP;
    $params = $language.":".$canVote.":".$multiVote;
    $infop = $info['language'].":".$info['canVote'].":".$info['multiVote'];
    if ($params != $infop) $fields['rating_id'] = $params;
    if ( $tpl != $info['tpl'] ) $fields['lastIP'] = $tpl;

    if (count($fields)){
        $where = "id = '1'";
        $modx->db->update($fields,$tbName,$where);
    }
  }

  function getUrl($curl = array()){
      if (empty($curl)) return $_SERVER['REQUEST_URI'];

      $uri = explode('?', $_SERVER['REQUEST_URI']);

      $i = 0;
      $qs = array();
	  $amp = ($this->config['xhtmlUrl']) ? '&amp;' : '&';

      foreach ($_GET as $param => $value) {
          if ($param != 'atrGrp' && $param != 'atrId' && $param != 'rating') {
            $param2 = htmlspecialchars($param, ENT_QUOTES);
            $value2 = htmlspecialchars($value, ENT_QUOTES);
            $qs[$param2] = $value2;
          }
      }

	  $qs = array_merge($qs, $curl);
	  if ($this->config['friendlyUrls']) unset ($qs['q']);

      return $uri[0] . '?' . http_build_query($qs, '', $amp);
  }

  function doVote($rating, $js = false) {
  
    global $modx;
    
    $grp = $this->config['atrGrp'];
    $id = $this->config['atrId'];
    $multiVote = $this->config['multiVote'];
    
    $expire = time() + 99999999;
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost

    if ($rating <= 100 && $rating >= 0){
        $where = "lastIP = '".$_SERVER['REMOTE_ADDR']."' AND rating_id = '$id'";
        $SELECT_RATING = "SELECT id FROM $this->tbRating WHERE $where;";

        if ( (!$multiVote) && ($modx->db->getRow($modx->db->query($SELECT_RATING)) || isset($_COOKIE[$grp.'_'.$id]))){
            if ($js) die("ERROR:".$this->lang['atr_already_voted']);
            else echo "<p class='already-voted'>".$this->lang['atr_already_voted']."</p>";
        } 
        else {
            if (strtotime($this->config['endDate']) > strtotime('now')) {

                if (!$multiVote) setcookie($grp.'_'.$id,$id,$expire,'/',$domain,false);

                $SELECT_IP = "SELECT id, rating, nbIP FROM $this->tbRating WHERE rating_id = '$id' ORDER BY voteDate ASC;";

                if ( $modx->db->getRecordCount($modx->db->query($SELECT_IP)) < $this->config['nbIP'] ){
                    $UPDATE_IP = "INSERT INTO $this->tbRating (rating_id, rating, lastIP, nbIP) VALUES ('$id','$rating','".$_SERVER['REMOTE_ADDR']."','1');";
                }
                else {
                    $data = $modx->db->getRow($modx->db->query($SELECT_IP));
                    $sum_rating = $rating + $data['rating']; // cumulated rating
                    $sum_nbIP = 1 + $data['nbIP'];
                    $updid = $data['id'];
                    $UPDATE_IP = "UPDATE $this->tbRating SET rating = '$sum_rating', lastIP = '".$_SERVER['REMOTE_ADDR']."',  nbIP = '$sum_nbIP' WHERE id = '$updid';";     
                }

                $modx->db->query($UPDATE_IP);
            }
            else {
                if ($js) die("ERROR:".$this->lang['atr_endDate']);
                else echo "<p class='enddate'>".$_lang['atr_enddate']."</p>";
            }
        }
    }
    else {
        $label = str_replace('%s',1,$this->lang['atr_more_less']);
        $label = str_replace('%n',$nbs,$label);
        if ($js) die("ERROR:".$label);
        else echo "<p class='more-less'>".$label."</p>";
    }

    if ($js) {
      $rating = $this->getRating();
      echo $this->getStarMessage($rating,$js);
    }
    
  }

  function getTopRated(){
  
    global $modx;
    global $table_prefix;

    $nbs = $this->config['nbStars'];

    if ( $this->config['topDir'] != 'best' && $this->config['topDir'] != 'worst' )
         $this->config['topDir'] = 'best';

    if ( (int) $this->config['topNb'] <= 0 ) $this->config['topNb'] = 5;

    $table = $table_prefix.$this->config['topTable'];
    if (! $this->existTable($modx->db->config['dbase'],$table))
       die("AnythingRating. The table $table doesn\'t exist!");

    $idField = $this->config['topIdField'];
    if (! $this->existField($table, $idField))
       die("AnythingRating. The field $idField doesn\'t exist!");

    $titleField = $this->config['topTitleField'];
    if (! $this->existField($table, $titleField))
      die("AnythingRating. The field $titleField doesn\'t exist!");

    $fields = "rating_id, ROUND((SUM(rating)/SUM(nbIP)),2) as score, ".
          "SUM(nbIP) as nbvotes, $table.$titleField as title";

    $descrField = $this->config['topDescrField'];  
    if ( ($descrField !="") && $this->existField($table, $descrField)) {
      $fields .= ", $table.$descrField as description";
      $useDescr = 1;
    }
    else $useDescr = 0;

    $imageField = $this->config['topImageField'];  
    if ( ($imageField !="") && $this->existField($table, $imageField)) {
      $fields .= ", $table.$imageField as image";
      $useImage = 1;
    }
    else $useImage = 0;

    $linkField = $this->config['topLinkField'];  
    if ( ($linkField !="") && $this->existField($table, $linkField)) {
      $fields .= ", $table.$linkField as link";
      $useLink = 1;
    }
    else $useLink = 0;

    $from = "$this->tbRating, $table";
    $where = "$table.$idField = rating_id GROUP BY rating_id";
    $dir = ($this->config['topDir'] == 'best') ? 'DESC' : 'ASC';
    $orderby = "score $dir, nbvotes $dir";
    $limit = $this->config['topNb'];

    $res = $modx->db->select($fields, $from, $where, $orderby, $limit);
    $i = 0;
    $results = "";

    if (!class_exists('atrChunkie')) 
        include_once($this->config['base']."/chunkie/chunkie.class.inc.php");
    $tpl = new atrChunkie($this->config['topTpl']);
    $template = "@CODE:".$tpl->template;

    while($data = $modx->db->getRow($res)){
        $i++;
        $score = round($data['score'] * $nbs /100, 2);
        $lbvotes = ($data['nbvotes'] == 1) ? $this->lang['atr_lbvote'] : $this->lang['atr_lbvotes']; 

        // get top rated template
        $tplt = new atrChunkie($template);
        $atr = array(
            'tpl' => 'result',
            'basepath' => $modx->config['base_path'],
            'baseurl' => $modx->config['base_url'],
            'usedescr' => $useDescr,
            'useimage' => $useImage,
            'uselink' => $useLink,
            'rank' => $i,
            'ratingid' => $data['rating_id'],
            'rating'  => $score,
            'nbstars' => $nbs,
            'lbstars' => $this->lang['atr_lbstars'],
            'nbvotes' => $data['nbvotes'],
            'lbvotes' => $lbvotes,
            'title' => $data['title'],
        );
        if ($useDescr) $atr['descr'] = $data['description'];
        if ($useImage) $atr['image'] = $data['image'];
        if ($useLink) $atr['link'] = $data['link'];

        $tplt->AddVar("atr",$atr);
        $results .= $tplt->Render()."\n";
        unset($atr);
        unset($tplt);
    }

    $label = ($this->config['topDir'] == 'worst') ? $this->lang['atr_worstlabel'] : $this->lang['atr_bestlabel'];
    $toplabel = str_replace('%s', $this->config['topLabel'], $label);

    // get top rated template
    $tplt = new atrChunkie($template);
    $atr = array(
        'tpl'=> "toprated",
        'usedescr' => $useDescr,
        'useimage' => $useImage,
        'uselink' => $useLink,
        'groupid' => $this->config['atrGrp'],
        'toplabel' => $toplabel,
        'hdrank' => $this->lang['atr_hdrank'],
        'hdscore'  => $this->lang['atr_hdscore'],
        'hdnbvotes' => $this->lang['atr_hdnbvotes'],
        'hdtitle' => $this->lang['atr_hdtitle'],
        'results' => $results
    );
    if ($useDescr) $atr['hddescr'] = $this->lang['atr_hddescr'];
    if ($useImage) $atr['hdimage'] = $this->lang['atr_hdimage'];

    $tplt->AddVar("atr",$atr);
    $output = $tplt->Render()."\n";

    return $output;
  }
  
}

/* The code below handles ratings sent if the $_POST variables are set. Use by ajax part */
if ($_POST['rating'] && $_POST['atrGrp'] && $_POST['atrId']) {

    $cfg = array(
        'atrGrp' => $_POST['atrGrp'], 
        'atrId' => $_POST['atrId'],
        'define' => false
    );

    // Setup the MODx API
    define('MODX_API_MODE', true);
    // initiate a new document parser
    include_once(MODX_MANAGER_PATH.'/includes/document.parser.class.inc.php');
    $modx = new DocumentParser;

    $modx->db->connect();
    $modx->getSettings();

    $atr2 = new AnythingRating($cfg);
    $atr2->doVote($_POST['rating'], true);
};

?>