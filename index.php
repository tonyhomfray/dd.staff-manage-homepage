<?php
$page_doctype='
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
';

$page_meta='
<meta name="description" content="" />
<meta name="Keywords" content="" />
<meta name="robots" content="index,follow" />
<meta name="revisit-after" content="7 days" />
<meta name="resource-type" content="document" />
<meta name="rating" content="Safe For Kids" />
<meta name="page-topic" content="University of Exeter" />
<meta name="copyright" content="University of Exeter" />
<meta name="author" content="Debbie Robinson" />
<meta http-equiv="reply-to" content="d.robinson@ex.ac.uk" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="DC.title" content="University of Exeter" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';

//load global styles only - add specific ones in as you need them favicon global.css 277 IE and print.css 1339
$page_styles= <<<EOF

<link rel="shortcut icon" href="/media/universityofexeter/webteam/styleassets/images/favicon.ico" />
<style type="text/css" media="all">@import "/media/universityofexeter/webteam/styleassets/css/global.css";</style>
<style type="text/css" media="print">@import "/media/universityofexeter/webteam/styleassets/css/print.css";</style>

EOF;

$page_title=''; //put your own in


$page_body_start='


<link rel="stylesheet" type="text/css" href="//www.exeter.ac.uk/codebox/cookie-policy-notification/styles.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
<script src="//www.exeter.ac.uk/codebox/cookie-policy-notification/notification.js"></script>


</head>
<body>

<div id="wrapper">
<div id="wrapperinner">

';

$page_top_nav= <<<EOF1

<!-- navigation object : Header --><style>
a#skip-to-content {
    left: -999px;
    position: absolute;
    top: auto;
    width: 1px;
    height: 1px;
    overflow: hidden;
    z-index: -999;
}
a#skip-to-content:focus, a#skip-to-content:active {
    color: #fff;
    background-color: #000;
    left: auto;
    top: auto;
    width: 30%;
    height: auto;
    overflow: auto;
    margin: 10px 35%;
    padding: 5px;
    border-radius: 15px;
    border: 4px solid yellow;
    text-align: center;
    font-size: 1.2em;
    z-index: 999;
}
</style>


<div id="header">
<a id="skip-to-content" class="sr-only sr-only-focusable" href="#pageheader" aria-label="Skip to main content">Skip to main content</a>
<a href="//www.exeter.ac.uk" title="Go back to the University of Exeter home page"><img src="//www.exeter.ac.uk/media/universityofexeter/webteam/styleassets/images/UOE-green.svg" alt="Go back to the University of Exeter home page" class="logo" width="162" height="60" /></a><div id="supernav"><p><a href="//www.exeter.ac.uk">Home</a> | <a href="//www.exeter.ac.uk/contact/">Contact us</a> | <a href="//www.exeter.ac.uk/staff/">Staff</a> | <a href="//www.exeter.ac.uk/students/">Students</a> | <a href="https://i.exeter.ac.uk/" onclick="pageTracker._trackEvent('utilityclickthrough', 'iExeter')" >iExeter (Staff and Students)</a> | <a href="//www.exeter.ac.uk/about/sitemap/">Site map</a> | <a href="http://www.universityofexeter.cn/" target="_blank" onclick="pageTracker._trackEvent('Global Nav', 'Chinese link clicked')">中文网</a></p>
</div>

<div id="search">
         <form action="https://search.exeter.ac.uk/s/search.html" method="get" name="htsearchform" id="searchbox">
       <input name="query" class="query" type="text" value="" accesskey="q" maxlength="80" title="Search the site" />
       <input class="button" value="Search"  type="submit" />
       <input type="hidden" value="uoe~sp-search" name="collection" />
     </form>
    </div>

</div>
 
<div id="mainnav">
<ul>
<li><a href="//www.exeter.ac.uk/study/" title="Study" class="studying">Study</a></li><li><a href="//www.exeter.ac.uk/research/" title="Research" class="research">Research</a></li><li><a href="//www.exeter.ac.uk/business/" title="Business" class="business">Business</a></li><li><a href="//www.exeter.ac.uk/alumnisupporters/" title="Alumni and supporters" class="alumni">Alumni and supporters</a></li><li><a href="//www.exeter.ac.uk/departments/" title="Our departments" class="schools">Our departments</a></li><li><a href="//www.exeter.ac.uk/visit/" title="Visiting us" class="visiting">Visiting us</a></li><li><a href="//www.exeter.ac.uk/about/" title="About us" class="about">About us</a></li>
</ul>
</div>



EOF1;

$breadcrumb= <<<EOF2

<div id="breadcrumb">
<a href="/">Home</a> > <a href="/staff/">Current staff</a> > <a href="/staff/manage/">Manage</a>
</div>

EOF2;

$page_content_top='
      <div id="content">
        <div class="prop minpx"></div>
';
$page_left_nav='';

/* put the appropriate left nav into this variable - but directly before content in T4 eg:
        <div id="leftnav">
          <ul><li><span><!-- navigation object : UG left menu top --></span></li></ul><!-- navigation object : UG left nav --><ul>
<li>
<ul><li><a href="/staff/manage/accommodationsearch/">Accommodation Search</a></li><li><a href="/staff/manage/abs/">Activity Booking system</a></li><li><a href="/staff/manage/beecon/">Beecon</a></li><li><a href="/staff/manage/capitalprojects/">Capital projects</a></li><li><a href="/staff/manage/daropreferences/">Daro Preferences</a></li><li><a href="/staff/manage/employability/">employability</a></li><li><a href="/staff/manage/exeterevents/">ExeterEvents</a></li><li><a href="/staff/manage/funding/">Funding</a></li><li><a href="/staff/manage/healthandsafety/">health and safety</a></li><li><a href="/staff/manage/ipams/">iPaMS</a></li><li><a href="/staff/manage/insessional/">insessional</a></li><li><a href="/staff/manage/residentials/">residentials</a></li><li><a href="/staff/manage/rkt/">RKT</a></li><li><a href="/staff/manage/scienceexeter/">science@Exeter</a></li></ul>
</li>
</ul>
        </div>
*/
        
$page_pre_rightnav='      
<div id="contentmainnav">
<div id="skipanchor"><a name="maincontent"></a></div>
';

$pageheader=''; // eg <div id="pageheader">Residentials</div>
        
$page_right_nav=''; //generate your own right nav

$page_footer= <<<EOF3

</div>
<div class="minclear"></div>
</div>
      
<!-- footer  nav object -->    
<div id="footer">
		<div id="footerleft"><p><a href="//www.exeter.ac.uk/usingoursite/">Using our site</a>&nbsp;|&nbsp;<a href="//www.exeter.ac.uk/foi/">Freedom of Information</a>&nbsp;|&nbsp;<a href="//www.exeter.ac.uk/dataprotection/">Data Protection</a>&nbsp;|&nbsp;<a href="//www.exeter.ac.uk/copyright/">Copyright &amp; disclaimer</a>&nbsp;|&nbsp;<a href="//www.exeter.ac.uk/privacy/">Privacy &amp; Cookies</a>&nbsp;|&nbsp;</p></div>
		<div id="footerright">
			<p><a href="http://www.facebook.com/exeteruni"><img src="/media/universityofexeter/webteam/styleassets/images/facebookicon.gif" alt="Facebook" width="16" height="16" /></a><a href="http://twitter.com/uniofexeter"><img src="/media/universityofexeter/webteam/styleassets/images/twittericon.png" alt="Twitter" width="16" height="16" /></a><a href="http://www.youtube.com/universityofexeter"><img width="16" height="16" alt="YouTube" src="/media/universityofexeter/webteam/styleassets/images/youtubeicon.gif" /></a><a href="https://www.linkedin.com/school/11826?pathWildcard=11826"><img src="/media/universityofexeter/webteam/styleassets/images/linkedinicon.gif" alt="LinkedIn" width="16" height="16" /></a>    




<!-- AddThis Button BEGIN
<a class="addthis_button" href="https://www.addthis.com/bookmark.php?v=250&amp;username=uniofexeter"><img src="//s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0" /></a><script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script><script defer="defer" type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js#username=uniofexeter"></script>
AddThis Button END -->


</p>
		</div>
      </div>

  </div>
</div>


<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-7878092-1");
pageTracker._setDomainName(".exeter.ac.uk");
pageTracker._trackPageview();
} catch(err) {}</script>
<!-- END of Google Analytics CODE -->

EOF3;

$page_end=
'</body>
</html>';

?><? include ('/mnt/webdata1/webs/www.exeter.ac.uk/docs/codebox/manage/list_available_to_user.php') ?>