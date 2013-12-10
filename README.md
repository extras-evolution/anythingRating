
AnythingRating
================================================================================

MODX Snippet for Ajax Dynamic Star Rating

Features:
----------------------------------------------------------------

- Uses AJAX Post (simple javascript, no framework like jQuery needed)
- Unobtrusive (works with javascript disabled)
- Use as many rating groups you want 
- Rate as many items you want without any TV definition
- Checks not only against IP upon vote but includes also a cookie check 
- Precise rating to a 2 decimal place
- Pre-loads common images
- Multi-languages (French, English, ...)
- Tested in IE 6, IE 7, Firefox 2.x, Opera and Safari

- Rating group - Define your rating group once only per page:
 - Choose the number of "stars" (or any other image)
 - Define an end date for the contest
 - Style the rating information you want with the template - eg: 3.25/5 stars 65% (205 votes)
 - Change the "star" image by changing the css style sheet
 - Precise the number of IP addresses stored. Store the more recent addresses
 - Decide if multi-vote is allowed or not
 - Choose if only registered users could vote

- Rate anything you want with a unique id:
 - Link your rated item to a rating group for contest
 - Ability to set 'novote' option to not allow users to vote for this item
 - Initialize the first rating from a named Template Variable of a document

- Display top rated items of a rating group:
 - Choose the number of top rated items
 - Choose to display the "best" or the "worst"
 - Define the linked information (title, description, image, link) from an other table
 - Style the top rated results you want with the template
 - Display as many you want your top rated lists

Weary of this contest? Simply drop the rating group table!

Installation:
--------------------------------------------------------------------------------

1. Upload the folder *assets/snippets/anythingrating* in the corresponding folder in your installation.
2. Create a new snippet called AnythingRating with contents of the file *install/assets/snippets/anythingRating.tpl*
3. Add the following minimal snippet calls on your page:
4. First on each page, a rating group definition: ``[!AnythingRating? &define=`1` &atrGrp=`grpName`!]`` where 'grpName' is the rating group name of items you want to rate.
5. Then anywhere in your page (usually near the item): ``[!AnythingRating? &atrGrp=`grpName` &atrId=`idItem`!]`` where 'idItem' is the id of each rated item. Could be [*id*] or [+maxigallery.picture.id+] with a maxigallery template or any other relevant id you want.
6. If you want display the top rated items on other pages: ``[[AnythingRating? &getTopRated=`1` &atrGrp=`grpName` &topTable=`site_content` &topTitle=`pagetitle`]]`` where 'site_content' is an example of table where to search the titles of rated items
7. May be edit the css/anythingRating.css file and adapt the styles to change how your rating widget looks
8. May be edit the templates/anythingRating.tpl.html file and adapt the template to change how your rating widget looks. Do the same with templates/topRated.tpl.html. Look at the possible placeholders to customize your templates
7. Enjoy anythingRating!

### Change the anythingRating folder location

To change the location of the anythingRating snippet folder :

1. change the definition of ATR_PATH in snippet code. `define('ATR_SPATH', 'assets/snippets/anythingRating/');`
2. change the definition of ATR_RELATIVE_BASE_PATH in *includes/anythingRating_process.php* `define('ATR_RELATIVE_BASE_PATH','../../../');`
3. in the *js/anythingRating.js* file change the _atrbase value: `var _atrbase = 'assets/snippets/anythingRating/';`

Parameters
--------------------------------------------------------------------------------

### General

Property | Description | Default
---- | ----------- | -------
define | Set the definition of a group of rated items | 0
getTopRated | Display top rated items | 0
atrGrp | **(Mandatory)** Unique rating group name – Any combination of characters a-z, underscores, and numbers 0-9 | ratings


### Rating group definition (define=1)

Property | Description | Default
---- | ----------- | -------
language | Snippet language | MODX manager_language
nbStars | Number of stars | 5
nbIP | Number of IP adresses stored | all
endDate | End date to vote | unlimited
canVote | Comma separated list of web user groups allowed to vote. The user should be logged-in as user of this web group. 'all' for a public vote without registration | all
multiVote | Allow a user to vote many times | 0
atrCss | Path & name of the css file to use | `assets/snippets/anythingRating/css/anythingRating.css`
atrTpl | Path & name of the template file to use | `assets/snippets/anythingRating/templates/anythingRating.tpl.html`

Look at css/anythingRating.css to style the text and the rating widget.


### Rated item

Property | Description | Default
---- | ----------- | -------
atrId | **(Mandatory)** Unique id for this anythingRating instance – Any combination of characters a-z, underscores, and numbers 0-9 | 0
noVotes | disallow (display votes only) / allow the vote for this item | 0
init | Initialize the vote with a rating value or from content of a template Variable | 0

init could be:
- a numeric value >=1 and <= &nbStars
- a data as follow TVname[:docId] where TVName is a name of TV. By default the value come from the TV of the current document. docId notice which document take into account for the TV TVName. The content of the TV should contain a rating_value or `rating_value:nb_votes`


### Get Top Rated items (getTopRated=1)

Property | Description | Default
---- | ----------- | -------
topNb | Number of rated items to be displayed | 5
topDir | Display the best or the worst rated items (`best`, `worst`) | `best`
topTpl | Path & name of the template file to use to display the top rated list | `assets/snippets/anythingRating/templates/topRated.tpl.html`
topLabel | Label of the top rated item used in the title of top/worst rated results (used by $_lang['atr_bestlabel'] and $_lang['atr_worstlabel'] in language files) | `items`
topTable | **(Mandatory)** Table name of the rated items | 
topIdField | Id field of the rated items | `id`
topTitleField | Title field of the rated items | `title`
topDescrField | Possible description field of the rated items |
topImageField | Possible image field of the rated items |
topLinkField | Possible link field of the rated items |

### Placeholders

A list of the available placeholders that could be used in the templates:

####AnythingRatingTpl

Look for the default content in `assets/snippets/anythingRating/templates/anythingRating.tpl.html`

Placeholder | Description
----------- | ----------- 
atr.scoreSection | score section
atr.intro | score introduction label
atr.rating | rating value
atr.nbstars | number of "stars"
atr.lbstars | label for "stars"
atr.nbvotes | number of votes
atr.lbvotes | label for "votes"
atr.msgSection | message section


####topRatedTpl
Look for the default content in `assets/snippets/anythingRating/templates/topRated.tpl.html

Placeholder | Description
----------- | -----------
atr.groupid | rating group name
atr.baseurl | web site base url
atr.ratingid | id of the rated item
atr.hdrank | label for "rank"
atr.hdscore | label for "score"
atr.hdnbvotes | label for "nb votes"
atr.hdtitle | label for "title"
atr.hddescr | label for "descr" see topDescrField parameter
atr.hdimage | label for "image" see topImageField parameter
atr.hdlink | label for "link" see topLinkField parameter
atr.rank | rank
atr.rating | score (rating value)
atr.nbstars | number of "stars"
atr.lbstars | label for "stars"
atr.nbvotes | number of votes
atr.lbvotes | label for "votes"
atr.title | title
atr.descr | description see topDescrField parameter
atr.image | image  see topImageField parameter
atr.link | link  see topLinkField parameter

Examples
--------------------------------------------------------------------------------

### Rating group definition 

```
[!AnythingRating? 
&define=`1`
&atrGrp=`photos`
!]
```

This simpliest snippet call defines the 'photos' rating group with:

- a storage of 'all' IP addresses of voters per item
- a widget with 5 images (define by default css file)
- an 'unlimited' date for this contest
- language set as the language of the MODx manager

```
[!AnythingRating?
&define=`1`
&atrGrp=`travelbook`
&language=`francais-utf8` 
&canVote=`travel`
&nbIP=`200`
&nbStars=`10`
&endDate=`2008-06-30`
&atrTpl=`@FILE:assets/snippets/anythingRating/templates/travelBookTpl.tpl.html` 
&atrCss=`@FILE:assets/snippets/anythingRating/css/travelBookCss.css`
!]
```

This snippet call defines the 'travelbook' rating group with:

- language set as francais-utf8
- only web users of the "travel" web group could vote
- a storage of a maximum of 200 IP addresses of voters per item
- a widget with 10 images (defined by the css file) 
- 2006-06-30 as the end date for the contest
- the file travelBookTpl.tpl.html as template file
- the file travelBookCss.css as css file

### Rated item 

```
[!AnythingRating?
&atrGrp=`travelbook`
&atrId=`[*id*]`
!]
```

- 'travelbook' is the rating group which regroup the rated items
- [*id*] is for example the id of the current document you want to rate

```
[[AnythingRating?
&atrGrp=`travelbooks`
&atrId=`[+id+]`
x&init=`opinion:[+id+]`
]]
```

- 'travelbook' is the rating group which regroup the rated items
- [+id+] is the id of your rated item (travelbook)
- the initial value of rating come from the TV 'opinion' of the document [+id+] (snippet called for example from the Ditto item list template)

```
[!AnythingRating?
&atrGrp=`photos` 
&atrId=`[+maxigallery.picture.id+]`
!]
```

- 'photos' is the rating group which regroup the rated items
- [+maxigallery.picture.id+] is for example the id of the image you want to rate (snippet called from the maxigallery picture template, to re-use the photo id)
 
These basic calls renders an anythingRating widget.

Language, images and display parameters are provided by the rating group `photos`

```
[!AnythingRating? 
&atrGrp=`products` 
&atrId=`[*id*]` 
&noVotes=`1` 
&init=`opinion`
!]
```

where:

- 'products' is the rating group which regroup the rated items
- [*id*] is for example the id of the current document you want to rate
- display the vote only (Votes not allowed)
- initialize the vote from the TV 'opinion'

### Get top Rated items 

```
[[AnythingRating?
&getTopRated=`1`
&atrGrp=`travelbook`
&topTableField=`site_content`
&topTitleField=`pagetitle`
&topLabel=`travelbooks`
]]
```
 
This simpliest snippet call display the top rated items of the 'travelbooks' contest:

- display the 5 best rated items
- id, title and description of rated items come from the 'site_content' table
- id field used is by default 'id'
- the label used for the title of the top rated results is 'travelbooks'
- title field use the pagetitle field of the 'site_content' table
- description, image and link fields are by default not used 
- the default template used is: assets/snippets/anythingRating/templates/topRated.tpl.html

```
[!AnythingRating?
&getTopRated=`1`
&topDir=`worst`
&atrGrp=`photos`
&topNb=`3`
&topTpl=`@FILE:assets/snippets/anythingRating/templates/anotherTopRated.tpl.html`
&topTable=`maxigallery`
&topDescrField=`descr`
&topImageField=`filename`
&topLinkField=`gal_id` 
&topLabel=`photos`!]
```
 
This snippet call display the top rated items of the 'photos' contest:

- display the 3 worst rated items!
- the template used is: assets/snippets/anythingRating/templates/anotherTopRated.tpl.html
- id, title and description of rated items come from the 'maxigallery' table
- id field and title field used are by default 'id' and 'title'
- description field, of the 'maxigallery' table, used is 'descr'
- image field used is 'filename'
- link field used is 'gal_id'
- the label used for the title of the worst rated results is 'photos'

Where find more support
----------------------------------------------------------------
1. Look at MODX Community Forums -> Add-ons -> General Repository Items Support -> [Support/Comments for AnythingRating](http://forums.modx.com/forums/thread/38448/support-comments-for-anythingrating#dis-post-219069)
2. MODX Docs -> Extras -> Evo Extras -> [AnythingRating](http://rtfm.modx.com/extras/evo/anythingrating)

Credits
----------------------------------------------------------------

Partly based on Ajax Dynamic Star Rating 1.6 by Jordan Boesch (www.boedesign.com)
and modified by Coroico (www.modx.wangba.fr)

Copyright & Licencing:
----------------------
Licensed under Creative Commons
http://creativecommons.org/licenses/by-nc-nd/2.5/ca/
