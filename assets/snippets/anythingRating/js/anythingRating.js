//    AnythingRating - 1.0
//
//    Based on Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)
//    as modified Coroico (www.modx.wangba.fr)
//
//    Licensed under Creative Commons - http://creativecommons.org/licenses/by-nc-sa/2.5/ca/
//

// AnythingRating Snippet folder location
var _atrbase = 'assets/snippets/anythingrating/';

// pre-load default images
if (document.images){
  pic1 = new Image(220,19); 
  pic1.src = _atrbase + "images/rating_loading.gif";  
  
  pic2 = new Image(16,13); 
  pic2.src = _atrbase + "images/rating_tick.gif";
  
  pic3 = new Image(14,14); 
  pic3.src = _atrbase + "images/rating_warning.gif";
}

var xmlHttp;

function GetXmlHttpObject(){

var xmlHttp = null;

  try {
    // Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
    }
  catch (e) {
    // Internet Explorer
    try {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e){
      xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}

function rate(rating,grp,id){

  xmlHttp = GetXmlHttpObject()

  if(xmlHttp == null){
    alert ("Your browser does not support AJAX!");
    return;
    }

  xmlHttp.onreadystatechange = function(){

  var msg = document.getElementById('msg_'+id);

    if(xmlHttp.readyState == 4){ 

      //msg.style.display = 'none';
      var res = xmlHttp.responseText;
      err = res.split('ERROR:')[1];

      if(err){
        msg.style.display = 'block';
        msg.innerHTML = '<div class="voted-twice">'+ err + '</div>';
      }
      else {
        r = res.split('||');
        var score = document.getElementById('score_'+id)
        score.style.display = 'block';
        score.innerHTML = '<div class="score">' + r[0] + '</div>';

        var rater = document.getElementById('rater_'+id);
        if (r[3]=='oneVote') rater.className = 'star-rating2';

        var all_li = rater.getElementsByTagName('li');

        if(navigator.appName == 'Microsoft Internet Explorer'){
          all_li[0].style.setAttribute('width',r[1]); // IE
        } else {
          all_li[0].setAttribute('style','width:'+ r[1]); // Everyone else
        }
        // start at 1 because the first li isn't a star
        //for(var i=1;i<all_li.length;i++){
          //all_li[i].getElementsByTagName('a')[0].onclick = 'return false;';
        //}

        msg.style.display = 'block';
        msg.innerHTML = '<div class="voted">' + r[2] + '</div>';

      }
    } else {
      msg.innerHTML = '<img src="'+ _atrbase + 'images/rating_loading.gif" alt="loading" />';
    }
  }

  var url = 'index-ajax.php';
   var q = _atrbase + "classes/anythingRating.class.inc.php";
  var params = "q="+q+"&atrGrp="+grp+"&atrId="+id+"&rating="+rating;

  xmlHttp.open("POST",url,true);
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.setRequestHeader("Content-length", params.length);
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.send(params);
}