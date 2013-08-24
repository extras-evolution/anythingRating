
AnythingRating Readme version 1.0

---------------------------------------------------------------
:: Snippet: anythingRating
----------------------------------------------------------------
  Short Description: 
        Ajax Dynamic Star Rating

  Version:
        1.0

  Created by:
      Coroico (coroico@wangba.fr)

----------------------------------------------------------------
:: Credits
----------------------------------------------------------------

   Partly based on Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)
   and modified by Coroico (www.modx.wangba.fr)

  Copyright & Licencing:
  ----------------------
   Licensed under Creative Commons
   http://creativecommons.org/licenses/by-nc-nd/2.5/ca/

---------------------------------------------------------------
:: Changelog:
----------------------------------------------------------------

  12-Apr-08 (1.0)
    -- Initial work based on Star Rating 1.6

----------------------------------------------------------------
:: Description
----------------------------------------------------------------

    Uses AJAX Post (simple js script, no JS library like mootools needed)
    Unobtrusive (works with javascript disabled)
    Use as many rating group you want 
    Rate as many items you want without any TV definition
    Checks not only against IP upon vote but includes also a cookie check 
    Precise rating to a 2 decimal place
    Pre-loads common images
    Multi-languages (French, English, ...)
    Tested in IE 6, IE 7, Firefox 2.x, Opera and Safari

    Rating group - Define your rating group once only per page:
      Choose the number of "stars" (or any other image)
      Define an end date for the contest
      Style the rating information you want with the template
        eg: 3.25/5 stars 65% (205 votes)
      Change the "star" image by changing the css style sheet
      Precise the number of IP addresses stored. Store the more recent addresses
      Decide if multi-vote is allowed or not
      Choose if only registered users could vote

    Rate anything you want with a unique id :
      Link your rated item to a rating group for contest
      Ability to set 'novote' option to not allow users to vote for this item
      Initialize the first rating from a named Template Variable of a document

    Display top rated items of a rating group:
      Choose the number of top rated items
      Choose to display the "best" or the "worst"
      Define the linked information (title, description, image, link) from an other table
      Style the top rated results you want with the template
      Display as many you want your top rated lists

    Weary of this contest ? Simply drop the rating group table!

----------------------------------------------------------------
:: General Parameters
----------------------------------------------------------------

    &define [1 | 0]
        set the definition of a group of rated items

    &getTopRated [1 | 0]
        display top rated items

    &atrGrp [Any combination of characters a-z, underscores, and numbers 0-9 | 'ratings']   (mandatory)
        unique rating group name

----------------------------------------------------------------
:: Rating group definition (&define=`1`)
----------------------------------------------------------------

    &language [language_name | manager_language ] (optional)
        with manager_language = $modx->config['manager_language'] by default

    &nbStars [ positive integer | 5 ]   (optional)
        number of stars

    &nbIP [positive integer | 'all']   (optional)
        number of IP adresses stored
        'all'' to store all IP addresses (default)

    &endDate [date | 'unlimited']   (optional)
        end date to vote.
        'unlimited' for an unlimited end date (default)

    &canVote [webGroupList | 'all' ]   (optional)
        comma delimited list of web user group allowed to vote
        the user should be logged-in as user of this web group
        'all' for a public vote without registration (default)

    &multiVote [1 | 0]   (optional)
        allow a user to vote many time.
        by default only one vote is allowed

    &atrCss [css file | 'assets/snippets/anythingRating/css/anythingRating.css']   (optional)
        path & name of the css file to use

    &atrTpl [template file | 'assets/snippets/anythingRating/templates/anythingRating.tpl.html']    (optional)
        path & name of the template file to use

----------------------------------------------------------------
:: Rated item
----------------------------------------------------------------

    &atrId [ Any combination of characters a-z, underscores, and numbers 0-9 | '0']   (mandatory)
        unique id for this anythingRating instance

    &noVotes [1 | 0]      (optional)
        disallow (display votes only) / allow the vote for this item

    &init [rating-init | 0 ]  (optional)
        initialize the vote with a rating value or from content of a template Variable
        rating-init should be:
        - a numeric value >=1 and <= &nbStars
        - a data as follow TVname[:DocId] where TVName is a name of TV
        By default the value come from the TV of the current document.
        TVName:docId notice which document take into account for the TV TVName

        The content of the TV should contain a rating_value or "rating_value:nb_votes"

        By default 0

----------------------------------------------------------------
:: Get Top Rated items      (&getTopRated=`1`)
----------------------------------------------------------------

    &topNb [positive integer | 5]    (optional)
        top number of rated items to be displayed

    &topDir ['worst' | 'best']    (optional)
        top direction Display the best or the worst rated items
        'best' by default

    &topTpl [template file | 'assets/snippets/anythingRating/templates/topRated.tpl.html']    (optional)
        path & name of the template file to use to display the top rated list

    &topLabel [ label | 'rated item']    (optional)
        label of the top rated item used in the title of top/worst rated results
        used by $_lang['atr_bestlabel'] and $_lang['atr_worstlabel'] in language files
        
    &topTable [any existing table name without MODx prefix]   (mandatory)
        table name of the rated items 

    &topIdField [id field name | 'id']    (mandatory)
        id field of the rated items
        'id' by default

    &topTitleField  [title field name | 'title']   (mandatory)
        title field of the rated items
        'title' by default

    &topDescrField  [description field name]    (optional)
        possible description field of the rated items

    &topImageField  [image field name]          (optional)
        possible image field of the rated items

    &topLinkField  [link field name]            (optional)
        possible link field of the rated items

----------------------------------------------------------------
:: CSS
----------------------------------------------------------------

    Look at css/anythingRating.css to style the text and the rating widget
    You can change the css file with the atrCss parameter

----------------------------------------------------------------
:: Placeholders                        
----------------------------------------------------------------

  Here's a list of the available placeholders that could be used or not in the templates.

AnythingRatingTpl
  Look for the default content in: assets/snippets/anythingRating/templates/anythingRating.tpl.html

  [+atr.scoreSection+]    score section
  [+atr.intro+]           score introduction label
  [+atr.rating+]          rating value
  [+atr.nbstars+]         number of "stars"
  [+atr.lbstars+]         label for "stars"
  [+atr.nbvotes+]         number of votes
  [+atr.lbvotes+]         label for "votes"
  [+atr.msgSection+]      message section


topRatedTpl
  Look for the default content in: assets/snippets/anythingRating/templates/topRated.tpl.html

  [+atr.groupid+]         rating group name

  [+atr.baseurl+]         web site base url  
  [+atr.ratingid+]        id of the rated item
  
  Header table:
  [+atr.hdrank+]          label for "rank"
  [+atr.hdscore+]         label for "score"
  [+atr.hdnbvotes+]       label for "nb votes"
  [+atr.hdtitle+]         label for "title"
  [+atr.hddescr+]         label for "descr"     see topDescrField parameter
  [+atr.hdimage+]         label for "image"     see topImageField parameter
  [+atr.hdlink+]          label for "link"      see topLinkField parameter

  Rated item in the table:
  [+atr.rank+]            rank
  [+atr.rating+]          score (rating value)
  [+atr.nbstars+]         number of "stars"
  [+atr.lbstars+]         label for "stars"
  [+atr.nbvotes+]         number of votes
  [+atr.lbvotes+]         label for "votes"
  [+atr.title+]           title
  [+atr.descr+]           description           see topDescrField parameter
  [+atr.image+]           image                 see topImageField parameter
  [+atr.link+]            link                  see topLinkField parameter

----------------------------------------------------------------
:: AnythingRating Mode Example Calls  - Rating group definition          
----------------------------------------------------------------
[!AnythingRating? &define=`1` &atrGrp=`photos`!]

  This simpliest snippet call defines the 'photos' rating group with :
  - a storage of 'all' IP addresses of voters per item
  - a widget with 5 images (define by default css file)
  - an 'unlimited' date for this contest
  - language set as the language of the MODx manager


[!AnythingRating? &define=`1` &atrGrp=`travelbook` &language=`francais-utf8` 
    &canVote=`travel` &nbIP=`200` &nbStars=`10` &endDate=`2008-06-30`
    &atrTpl=`@FILE:assets/snippets/anythingRating/templates/travelBookTpl.tpl.html` 
    &atrCss=`@FILE:assets/snippets/anythingRating/css/travelBookCss.css`!]

  This snippet call defines the 'travelbook' rating group with :
  - language set as francais-utf8
  - only web users of the "travel" web group could vote
  - a storage of a maximum of 200 IP addresses of voters per item
  - a widget with 10 images (defined by the css file) 
  - 2006-06-30 as the end date for the contest
  - the file travelBookTpl.tpl.html as template file
  - the file travelBookCss.css as css file

----------------------------------------------------------------
:: AnythingRating Mode Example Calls  - Rated item          
----------------------------------------------------------------

[!AnythingRating? &atrGrp=`travelbook` &atrId=`[*id*]`!]

    where:
    - 'travelbook' is the rating group which regroup the rated items
    - [*id*] is for example the id of the current document you want to rate



[[AnythingRating? &atrGrp=`travelbooks` &atrId=`[+id+]` &init=`opinion:[+id+]`]]

    where:
    - 'travelbook' is the rating group which regroup the rated items
    - [+id+] is the id of your rated item (travelbook)
    - the initial value of rating come from the TV 'opinion' of the document [+id+]
      (snippet called for example from the Ditto item list template)



[!AnythingRating? &atrGrp=`photos` &atrId=`[+maxigallery.picture.id+]`!]

    where:
    - 'photos' is the rating group which regroup the rated items
    - [+maxigallery.picture.id+] is for example the id of the image you want to rate
      (snippet called from the maxigallery picture template, to re-use the photo id)
    
    These basic calls renders an anythingRating widget 
    Language, images and display parameters are provided by the rating group `photos`


[!AnythingRating? &atrGrp=`products` &atrId=`[*id*]` &noVotes=`1` &init=`opinion` !]

    where:
    - 'products' is the rating group which regroup the rated items
    - [*id*] is for example the id of the current document you want to rate
    - display the vote only (Votes not allowed)
    - initialize the vote from the TV 'opinion'


----------------------------------------------------------------
:: AnythingRating Mode Example Calls  - Get top Rated items          
----------------------------------------------------------------

[[AnythingRating? &getTopRated=`1` &atrGrp=`travelbook` &topTableField=`site_content`
    &topTitleField=`pagetitle` &topLabel=`travelbooks` ]]
    
  This simpliest snippet call display the top rated items of the 'travelbooks' contest:
  - display the 5 best rated items
  - id, title and description of rated items come from the 'site_content' table
  - id field used is by default 'id'
  - the label used for the title of the top rated results is 'travelbooks'
  - title field use the pagetitle field of the 'site_content' table
  - description, image and link fields are by default not used 
  - the default template used is: assets/snippets/anythingRating/templates/topRated.tpl.html


[!AnythingRating? &getTopRated=`1` &topDir=`worst` &atrGrp=`photos` &topNb=`3` 
    &topTpl=`@FILE:assets/snippets/anythingRating/templates/anotherTopRated.tpl.html` 
    &topTable=`maxigallery` &topDescrField=`descr` &topImageField=`filename` 
    &topLinkField=`gal_id` &topLabel=`photos`!]
    
  This snippet call display the top rated items of the 'photos' contest:
  - display the 3 worst rated items !
  - the template used is: assets/snippets/anythingRating/templates/anotherTopRated.tpl.html
  - id, title and description of rated items come from the 'maxigallery' table
  - id field and title field used are by default 'id' and 'title'
  - description field, of the 'maxigallery' table, used is 'descr'
  - image field used is 'filename'
  - link field used is 'gal_id'
  - the label used for the title of the worst rated results is 'photos'


-----------------------------------------------------------------
:: How-to use this snippet
-----------------------------------------------------------------

1. Copy the contents of the file snippet.anythingRating.php into a new snippet named AnythingRating

2. Create a directory named anythingRating under the assets/snippets folder.

3. Copy the files from the zip into the anythingRating folder.

4. Add the following snippet calls:  

  First on each page, a rating group definition : 
  [!AnythingRating? &define=`1` &atrGrp=`grpName`!]

    where 'grpName' is the rating group name of items you want to rate

  then anywhere in your page (usually near the item :-) ):
  [!AnythingRating? &atrGrp=`grpName` &atrId=`idItem`!]
  
    where 'idItem' is the id of each rated item. could be [*id*]
    or [+maxigallery.picture.id+] with a maxigallery template or any other relevant id you want

  And if you want display the top rated items on another page:
  [[AnythingRating? &getTopRated=`1` &atrGrp=`grpName` &topTable=`site_content` &topTitle=`pagetitle`]]
  
    where 'site_content' is an example of table where to search the titles of rated items


5. May be edit the css/anythingRating.css file and adapt the styles to change
   how your rating widget looks
    
6. May be edit the templates/anythingRating.tpl.html file and adapt the template to change
   how your rating widget looks. Do the same with templates/topRated.tpl.html
   Look at the possible placeholders to customize your templates

7. Enjoy anythingRating!


----------------------------------------------------------------
:: How-to change the anythingRating folder location
----------------------------------------------------------------

To change the location of the anythingRating snippet folder :

1. change the definition of ATR_PATH in snippet.anythingRating.php

// Path where anythingRating is installed
define('ATR_SPATH', 'assets/snippets/anythingRating/');

2. change the definition of ATR_RELATIVE_BASE_PATH in includes/anythingRating_process.php

define('ATR_RELATIVE_BASE_PATH','../../../');

3. in the js/anythingRating.js file change the _base value:

var _atrbase = 'assets/snippets/anythingRating/';


----------------------------------------------------------------
:: Where find more support
----------------------------------------------------------------
1. Look at :

    Modx Community forum >> support >> Repository Items Support >> support/comments for anythingRating


2. Documentation : http://wiki.modxcms.com/index.php/AnythingRating

3. Demo site : http://modx.wangba.fr/anythingrating.html
