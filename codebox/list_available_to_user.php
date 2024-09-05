<?php
/*=================================================================
Tue Nov 16 09:05:53 GMT 2010

Display any utilities accessible to logged on user
=================================================================*/

ini_set("session.cookie_httponly", 1);
ini_set('default_charset', 'UTF-8');
header("X-Frame-Options: SAMEORIGIN");
header('Content-type: text/html; charset=utf-8');
session_name('opendayAdmin');
session_start();
//commented out so that session validation persists into applications - J.Toomey 16/01/19
//header("Cache-Control: no-cache, must-revalidate");


$_SESSION=array();
$_SESSION['admin']=filter_var($_SERVER['REMOTE_USER'], FILTER_SANITIZE_STRING, 100);
$thisyear=date('Y');
$_SESSION['logged_in_user']=$_SESSION['admin'];


$admin=filter_var($_SERVER['REMOTE_USER'], FILTER_SANITIZE_STRING, 100);
if ($admin=='jaaylwar' && isset($_GET['user'])) {
    $admin=filter_var($_GET['user'], FILTER_SANITIZE_STRING, 100);
}
$count=0;

//search LDAP
$user_details=LDAP_searcha('uid='.$admin);
$name=$user_details->fullname;
$email=$user_details->email;
$is_staff=$user_details->status1=="Staff";
$dept=$user_details->dept;

if (!$user_details->username) {
    die("Please contact the system administrator if you think you should have access to this page");
}


/*=================================================================
General permissions
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "webform", "br0W#s3.R", "xForms");
$admin_user="";
$stmt=$mysqli->prepare("SELECT m_sessions FROM manage_access WHERE m_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->store_result();
$num_rows=$stmt->num_rows;
$stmt->bind_result($user_sessions);
$stmt->fetch();
$stmt->close();

if ($num_rows==0) {
    die("Sorry - you don't currently have access to this page. Please contact the system administrator if you think you should");
}


$user_ip = getUserIP();

// if (! preg_match('/^144|10\.173\./', trim($user_ip))) {
//     die("Exeter only access - $user_ip  please use <a href='http://as.exeter.ac.uk/it/network/vpn/'>VPN</a> if you see this message");
// }

if ($user_sessions) {
    $sessions=preg_split('/,/', $user_sessions);
    for ($i=0;$i<count($sessions);$i++) {
        $_SESSION[$sessions[$i]]=true;
    }
}


if ($_SESSION['carparkscanner']) {
    setcookie("carparkscanner", 1);
}
print_header();
?>



<div style="width:1000px">
<a href="https://ssologin.exeter.ac.uk/distauth/UI/Logout" style="float:right">Log out</a>
<h1>Databases and Spreadsheets available to you: (<?php echo $_SESSION['logged_in_user'] ?>)</h1>
<?php
if (!$_SERVER['REMOTE_USER']) {
    ?>
<p><strong>Please note<br />There is currently an issue with identifying the logged in user, which is being investigated.<br />If you see an "Access denied" message, please come back later.</strong></p>

<?php
}




if ($_SESSION['ABS']) {
    $count++;

    echo "<div class=\"box\">
<h2><a href=\"https://www.exeter.ac.uk/staff/manage/abs/activerequests/\">Activities Booking system</a></h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/abs/myallocations/\">My allocations</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/abs/activerequests/\">Active requests</a></li>\n
</ul>\n
<hr />
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/teachers/activities/booking/login/\">School staff Login</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/teachers/activities/booking/calendar/\">School calendar</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/teachers/activities/booking/request/\">School activity request form</a></li>\n
</ul>\n
</div>\n";
}




/*=================================================================
Accommodation search edits
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "webform", "br0W#s3.R", "xForms");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_username FROM accAdmin WHERE a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['accSearchAmin']=$admin_user;
    echo "<div class=\"box\">
	<div class=\"plus\">+</div>
<h2>Accommodation Search</h2>\n
	<ul>\n
	<li><a href=\"/staff/manage/accommodationsearch/editresidences/\">Edit Residences</a></li>\n
	<li><a href=\"/staff/manage/accommodationsearch/editroomtypes/\">Edit Room types</a></li>\n
	<li><a href=\"/staff/manage/accommodationsearch/editoccupantypes/\">Edit occupant types</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Campus Map objects
=================================================================*/

if ($_SESSION['geolocationdbaccess']) {
    $count++;
    echo "
<div class=\"box\">
<h2>Campus Maps</h2>\n
<ul>\n
<li><a href=\"geolocations/\">Edit map objects</a></li>
<li><a href=\"/googlemaps/mapxml.php/\" title='Save as streathamMap.xml and upload to /www.exeter.ac.uk/codebox/interactiveMap'>Xml file </a></li>
</ul>\n
</div>\n";
}

/*=================================================================
Campus Services Training requests
=================================================================*/
$mysqli = new mysqli('mysql.ex.ac.uk', 'xCamServ', '84rkl4y84nk3r', 'xCamServ');
$stmt=$mysqli->prepare("select ad_username from cs_live_admin where ad_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['cstrainingAdmin']=true;
    echo "<div class=\"box\">
<h2>Campus Services</h2>\n
	<ul>\n<li><a href=\"/staff/manage/campusservices/trainingrequests/\">Manage training budgets</a></li>\n
</ul>\n
</div>\n";
}



/*=================================================================
Campus Tours
=================================================================*/
$admin_user="";
$mysqli = new mysqli('mysql.ex.ac.uk', 'enqu1rer', 'x3nQur.3r', 'xDays');
$stmt=$mysqli->prepare("SELECT ct_username,ct_edit_dates,ct_superuser FROM campus_tours_admin WHERE ct_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user, $can_edit_dates, $is_superuser);
$stmt->fetch();
$stmt->close();
echo "<!-- $admin_user,$can_edit_dates ".(($can_edit_dates & 3)==3)."-->";
if ($admin_user) {
    $count++;
    $_SESSION['campus_tour_admin']=true;
    echo "<div class=\"box\">
<h2>Campus Tours administration</h2>\n
	<ul>\n<li><a href=\"/staff/manage/campustours/\">Manage bookings</a></li>\n";
    
    if (($can_edit_dates & 3)==3) {
        $_SESSION['can_edit_exCT_dates']=true;
        echo "<li><a href=\"/staff/manage/campustours/edittours/\">Edit Exeter campus tour dates</a></li>\n";
        echo "<li><a href=\"/staff/manage/campustours/editmessages/\">Edit Campus Tour messages</a></li>\n";
    }
    if (($can_edit_dates & 4)==4) {
        $_SESSION['can_edit_cwCT_dates']=true;
        echo "<li><a href=\"/staff/manage/campustours/editcornwalltours/\">Edit Cornwall campus tour dates</a></li>\n";
    }

    if ($is_superuser) {
        echo "<li><a href='/staff/manage/campustours/manageaccess/'>Manage access</a></li>\n";
    }
    echo "</ul>\n
</div>\n";
    /*============================================
    echo "<div class=\"box\">
    <h2><a href=\"/staff/manage/enhancedcampustours/\" >Campus Tours<br />(Enhanced programme)</a></h2>\n
        <ul>\n<!-- <li>Manage bookings</li> -->\n";
    if (($can_edit_dates & 3)==3) {
        $_SESSION['can_edit_exCT_dates']=true;
        echo "<li><a href=\"/staff/manage/enhancedcampustours/editpresentations/index.php?prefix=${thisyear}ex\">Edit Exeter programme</a></li>\n";
    }
    if (($can_edit_dates & 4)==4) {
        $_SESSION['can_edit_cwCT_dates']=true;
        echo "<li><a href=\"/staff/manage/enhancedcampustours/editpresentations/index.php?prefix=${thisyear}cw\">Edit Penryn programme</a></li>\n";
    }
    echo "
    <li><a href=\"/staff/manage/enhancedcampustours/index.php?campus=ex\">View Exeter bookings</a></li>
    <li><a href=\"/staff/manage/enhancedcampustours/index.php?campus=cw\">View Penryn bookings</a></li>
    <li style='border-top:1px solid grey'><a href=\"/staff/manage/enhancedcampustours/applicant/index.php?prefix=${thisyear}ex\">Search for Exeter enquirer</a></li>
    <li><a href=\"/staff/manage/enhancedcampustours/applicant/index.php?prefix=${thisyear}cw\">Search for Penryn enquirer</a></li>
    ";
    if (($can_edit_dates & 7)>0) {
            echo "<li style='border-top:1px solid grey'><a href=\"/staff/manage/campustours/enhancedmessages/index.php\">Edit html and email responses</a></li>\n";
    }
    
    echo"
    </ul>\n
    </div>\n";
    ==================================*/
}


/*=================================================================
Capital Project comments
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "cAmpu5", "sP3c.sAvr", "capitalProjects");
$admin_user="";
$stmt=$mysqli->prepare("select * from admin where a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user, $admin_name, $admin_surname);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['capital_projects_admin']=true;
    echo "
<div class=\"box\">
<h2>Capital Project comments</h2>\n
<ul>\n
		<li><a href=\"capitalprojects/\">View data</a></li>
</ul>\n
</div>\n";
}

/*=================================================================
Car parking permits
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "d1es3l", "FuM.3eS#", "parking_permits");
$admin_user="";
$stmt=$mysqli->prepare("select a_username from admin where a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['car_parking_admin']=true;
    echo "
<div class=\"box\">
<h2>Car parking permits</h2>
<ul>\n
<li><a href=\"carparkingpermits/\">Preview / download data</a></li>
</ul>\n
</div>\n";
}
/*=================================================================
Clearing, Adjustment, International
=================================================================*/

if ($_SESSION['clearing_admin']) {
    $count++;
    echo '
<div class="box">
<div class="plus">+</div>
<h2><a href="https://vacancies.exeter.ac.uk/admin/">Clearing, Adjustment, International</a></h2>
<ul>
<li><a href="https://vacancies.exeter.ac.uk/admin/clearing/all_exeter/">Manage all Exeter subjects</a></li>
<li><a href="https://vacancies.exeter.ac.uk/admin/clearing/all_cornwall/">Manage all Penryn subjects</a></li>
</ul>
</div>';
}

/*=================================================================
Custom Forms
=================================================================*/

if ($_SESSION['Custom_Forms']) {
    $count++;
    echo '
<div class="box">
<div class="plus">+</div>
<h2>Custom Forms</h2>
<ul>
<li><a href="http://www.exeter.ac.uk/staff/manage/customforms/">Manage your web forms</a></li>
</ul>
</div>

<div class="box">
<div class="plus">+</div>
<h2>Custom Form Builder (new version)</h2>
<ul>
<li><a href="http://www.exeter.ac.uk/staff/manage/customformbuilder/">Manage your web forms</a></li>
<li><a href="http://www.exeter.ac.uk/staff/manage/customformbuilder/listformpermissions/">View/Edit Forms and access</a></li>
</ul>
</div>
';
}

/*=================================================================
Daro Profiles
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "xDaroVolunteers", "5t39u9", "xDaroVolunteers");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_user FROM profiles_admin WHERE a_user=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['profiles_admin']=true;
    echo "<div class=\"box\"><div class=\"plus\">+</div>
<h2><a href=\"/staff/manage/daro/profiles/filter/\">Daro Profile</a></h2>\n
	<ul>\n<li><a href=\"/staff/manage/daro/profiles/\">List profiles</a></li>\n
	<li><a href=\"/staff/manage/daro/profiles/\">Search profiles</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Daro profile updates
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "xDaroVolunteers", "5t39u9", "xDaroVolunteers");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_user FROM admin_daro_updates WHERE a_user=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['daro_profile_updates']=true;
    echo "<div class=\"box\">
<h2>Daro Profile Updates</h2>\n
	<ul>\n<li><a href=\"/staff/manage/daro/profileUpdates/\">View/download profile updates</a></li>\n
</ul>\n
</div>\n";

    echo "<div class=\"box\">
<h2>Daro Memories of Exeter submissions</h2>\n
	<ul>\n<li><a href=\"/staff/manage/daro/memories/index.php/\">View/download submissions</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Daro Volunteers
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "xDaroVolunteers", "5t39u9", "xDaroVolunteers");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_user FROM admin WHERE a_user=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['daro_volunteers']=true;
    echo "<div class=\"box\">
<h2>Daro Volunteers</h2>\n
	<ul>\n<li><a href=\"/staff/manage/darovolunteers/\">View/download submissions</a></li>\n
</ul>\n
</div>\n";
}



/*=================================================================
Employability sign up to bulletins form
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "webform", "br0W#s3.R", "xForms");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_username FROM careers_admin WHERE a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['careers_admin']=true;
    echo "<div class=\"box\">
<h2>Employability Bulletin signups</h2>\n
	<ul>\n<li><a href=\"/staff/manage/employability/bulletin/\">View/download list of signups</a></li>\n
</ul>\n
</div>\n";
}

/*=================================================================
Engineering Interview days
=================================================================*/

if ($_SESSION['eng_interview']) {
    $count++;

    echo "<div class=\"box\">
<h2><a href=\"https://www.exeter.ac.uk/staff/manage/cemps/interviewdays/?dept=eng_\">Engineering Interview Days</a></h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/cemps/interviewdays/?dept=eng_\">Engineering Interview Days</a></li>\n

</ul>\n
</div>\n";
}



/*=================================================================
Exeter Events
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "Bth3re0rB", "@Eta0f12.44x", "exeter_events");
$admin_user="";
$stmt=$mysqli->prepare("
SELECT 'admin' FROM administrators WHERE a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['exeter_events_admin']=$admin;
    echo "<div class=\"box\">
<h2>Exeter Events</h2>\n
<ul>\n
<li><a href=\"exeterevents/\">List, add or edit events</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Flagit
=================================================================*/

if ($_SESSION['FlagIt']) {
    $count++;
    echo "
<div class=\"box\">
<h2>Flag it</h2>\n
<ul>\n
<li><a href=\"flagit/\">Manage flagged items and responses</a></li>
</ul>\n
</div>\n";
}



/*=================================================================
Funding
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "cCl0ggs", "m3nS4r@ex4", "xScholarships");
$admin_user="";
$stmt=$mysqli->prepare("SELECT username FROM administrators WHERE username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['funding_admin']=true;
    echo "<div class=\"box\">
<h2>Funding database</h2>\n
<ul>\n<li><a href=\"funding/\">Admin utilities</a></li>
<li><a href=\"funding/application/\">Applications</a></li>
<li><span style='padding-left:10px'>Award number:</span><input size='2' id='a_id' style='max-width:3em' type='text'>&nbsp;<a href='#' onclick=\"window.location='funding/addeditaward/?schol='+document.getElementById('a_id').value\" style='display:inline'>Edit</a></li>
<li><a href='/staff/manage/funding/listawards/'>List awards</a></li>
<li><a href='/staff/manage/funding/addeditadministrators/'>Edit Administrators</a></li>
</ul>\n
</div>\n";
}

/*=================================================================
Dave Salway's health and safety
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "n0rm@N", "ZxX1.925", "xEthics");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_username FROM hs_admin WHERE a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['ideas_admin']=true;
    echo "<div class=\"box\">
<h2>Health and Safety declarations</h2>\n
	<ul>\n<li><a href=\"/staff/manage/healthandsafety/\">View/download submissions</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
iPaMs
=================================================================*/

if ($_SESSION['ipams']) {
    $count++;
    echo "
<div class=\"box\">
<div class=\"plus\">+</div>
<h2>iPaMs</h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/\">UG Admin index</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/programmesearchandupdate/\">UG Programme search</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/modulesearchandupdate/\">UG Module search</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/testlist/\">UG Programmes in web database</a></li>
</ul>\n
<hr />
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/postgraduate\">PG Admin index</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/postgraduate/programmesearchandupdate/\">PG Programme search</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/postgraduate/modulesearchandupdate/\">PGModule search</a></li>
<li><a href=\"https://www.exeter.ac.uk/staff/manage/ipams/postgraduate/testlist/\">PG Programmes in web database</a></li>
</div>\n";
}

/*=================================================================
Offer Holder visit days
=================================================================*/

$mysqli = new mysqli('mysql.ex.ac.uk', 'enqu1rer', 'x3nQur.3r', 'xDays');
$admin_user="";
$stmt=$mysqli->prepare("
SELECT IF(superuser,'admin','user') FROM poods_admin WHERE username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['poods_admin']=true;
    echo "<div class=\"box\"><div class=\"plus\">+</div>
<h2><a href=\"offerholdervisitdays/\">Offer Holder Visit Days</a></h2>\n
<ul>
<li><a href=\"offerholdervisitdays/presentations/\">Presentations/Bookings</a></li>
<li><a href=\"offerholdervisitdays/students/\">Log in as if student</a></li>
<li><a href=\"offerholdervisitdays/students/index.php?type=1\">List students who haven&rsquo;t booked</a></li>
<li><a href=\"offerholdervisitdays/students/index.php?type=2\">List students who have booked</a></li>
</ul>";

    if ($admin_user=='admin') {
        echo "
<ul>
<li><a href=\"offerholdervisitdays/editdays/\">Add edit Post offer Open Day</a></li>
<li><a href=\"offerholdervisitdays/manage/\">Manage Admin users</a></li>
</ul>
";
    }
    echo "</div>\n";
}


/*=================================================================
Open days
=================================================================*/
$mysqli=new mysqli('mysql.ex.ac.uk', 'enqu1rer', 'x3nQur.3r', 'xDays');
$admin_user="";
$stmt=$mysqli->prepare("select a_username,a_superuser from opendays_admin where a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user, $is_superuser);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['opendays_admin']=true;
    echo "
<div class=\"box\"><div class=\"plus\">+</div>
<h2><a href='opendays/index.php'>Open Days management</a></h2>\n
<ul>\n
<li><a href=\"opendays/preregistration/\">Pre-registration numbers</a></li>
<li><a href=\"opendays/bookings/\">View bookings - download spreadsheet</a></li>
<li><a href=\"opendays/edit_applicant/\">Search/Edit booked attendee</a></li>
<li><a href=\"opendays/presentationbookings/\">Presentation bookings</a></li>
<li><a href=\"opendays/presentations/\">Add/Edit Exeter Open Day Presentations</a></li>
<li><a href=\"opendays/presentations/?campus=4\">Add/Edit Penryn Open Day Presentations</a></li>
<li><a href=\"opendays/edit_messages/\">Edit Open Day messages</a></li>
<li><a href=\"opendays/edit_days/\" title='Add,Edit Open or Close Open days and alter the maximum number that can attend' >Manage days</a></li>
";
    if ($is_superuser) {
        echo "<li><a href='opendays/manageaccess/'>Manage access</a></li>";
    }
    echo "
</ul>\n
</div>\n";
}

/*=================================================================
PG Open days
=================================================================*/
$mysqli=new mysqli('mysql.ex.ac.uk', 'enqu1rer', 'x3nQur.3r', 'xDays');
$admin_user="";
$stmt=$mysqli->prepare("select a_username from opendays_admin where a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['opendays_admin']=true;
    $html = "<div class=\"box\">";
    $html .= "<h2>Postgraduate Open Days management</h2>\n";
    $html .= "<ul>\n<li><a href=\"opendays/postgraduateopendays/\">Manage Exeter / Penryn Bookings</a></li>";
    $html .= "<li><a href=\"opendays/postgraduateopendays/editpostgraduateopendaymessages/\">Edit Exeter PG Open Day messages</a></li>\n";
    $html .= "<hr /><li><a href=\"opendays/postgraduateopendays/editpostgraduateopendaymessages/?cwpgopenday=true\">Edit Penryn PG Open Day messages</a></li></ul>\n";
    $html .= "<hr></div>\n";
    echo $html;
}


/*=================================================================

<ul>\n
<li><a href=\"opendays/postgraduateopendays/bookings/?prefix=2018&eventID=15\">View Penryn bookings - download spreadsheet</a></li>
<li><a href=\"opendays/postgraduateopendays/editpostgraduateopendaymessages/?cwpgopenday=1\">Edit Penryn PG Open Day messages</a></li>
</ul>\n

Open day Marshalls download


if ($_SESSION['student_ambassadors']){
$count++;

echo "<div class=\"box\">
<h2><a href=\"https://www.exeter.ac.uk/staff/manage/opendays/marshalls/index.php\">Open Day marshals</a></h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/opendays/marshalls/index.php?campus=exeter\">Exeter</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/opendays/marshalls/index.php?campus=cornwall\">Cornwall</a></li>\n
</ul>\n
</div>\n";

}
=================================================================*/

/*=================================================================
Open day Marshalls download
=================================================================*/

if ($_SESSION['physicsInterview']) {
    $count++;

    echo "<div class=\"box\">
<h2><a href=\"https://www.exeter.ac.uk/staff/manage/physics/visitdays/?dept=phy_\">Physics Interview Days</a></h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/physics/visitdays/?dept=phy_\">Single Honours</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/physics/visitdays/?dept=map_\">Combined Honours</a></li>\n

</ul>\n
</div>\n";
}

/*=================================================================
Prospectus
=================================================================*/

if ($_SESSION['prospectus']) {
    $count++;
    echo "
<div class=\"box\">
<h2>Prospectus enquiries</h2>\n
<ul>\n
<li><a href=\"/prospectus/admin/\" >Prospectus spreadsheet generator</a></li>
</ul>\n
</div>\n";
}


/*=================================================================
PSRA nominations
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "webform", "br0W#s3.R", "xForms");
$admin_user="";
$stmt=$mysqli->prepare("SELECT a_username FROM 2012_psra_admin WHERE a_username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['psra_admin']=$admin_user;
    echo "<div class=\"box\">
<h2>PSRA nominations</h2>\n
	<ul>\n<li><a href=\"/staff/manage/psra/\">View/download Nominations</a></li>\n
</ul>\n
</div>\n";
}

/*=================================================================
Reception Visitors
=================================================================*/

if ($_SESSION['Reception Visitors']) {
    $count++;
    echo "
<div class=\"box\">
<h2>Reception Visitors</h2>\n
<ul>\n
<li><a href='//www.exeter.ac.uk/staff/manage/gpvisitors/schedule/'>Geoffrey Pope</a></li>
<li><a href='//www.exeter.ac.uk/staff/manage/hatherlyvisitors/schedule/'>Hatherly</a></li>
<li><a href='//www.exeter.ac.uk/staff/manage/lsivisitors/schedule/'>LSI</a></li>
<li><a href='//www.exeter.ac.uk/staff/manage/visitors/schedule/'>Northcote House</a></li>
<li><a href='//www.exeter.ac.uk/staff/manage/innovation/schedule/'>Innovation</a></li>
</ul>\n
<ul>
<li><a href='https://www.exeter.ac.uk/staff/manage/visitors/admin/'>Edit Access</a></li>
</ul>
</div>\n";
}

/*=================================================================
Research Access
=================================================================*/

if ($_SESSION['researchaccess']) {
    $count++;

    echo "<div class='box'>
<h2><a href='http://www.exeter.ac.uk/staff/manage/researchaccess/'>Research Access</a></h2>\n
<ul>\n
<li><a href='http://www.exeter.ac.uk/staff/manage/researchaccess/'>Edit Questions</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Karls Residentials

<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=pup\">Pre Uni Physics</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=PPE\">PPE Summer school</a></li>\n
=================================================================*/

if ($_SESSION['Residentials']) {
    $count++;

    echo "<div class=\"box\">
<h2><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php/\">Residential bookings</a></h2>\n
<ul>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=10_300316\">Year 10 Residential March 29th-30th 2016 bookings</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=10_010416\">Year 10 Residential March 31st - April 1st 2016 bookings</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=11\">Year 11 bookings</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=12\">Year 12 bookings</a></li>\n
<li><a href=\"https://www.exeter.ac.uk/staff/manage/residentials/admin/index.php?r=SSyr12\">Social Science Yr 12  Residential</a></li>\n
</ul>\n
</div>\n";
}

/*=================================================================
Rumour buster
=================================================================*/

if ($_SESSION['rumour_buster']) {
    $count++;
    echo "
<div class=\"box\">
<h2>Rumour buster</h2>\n
<ul>\n
<li><a href=\"rumours/\">Manage rumour buster submissions</a></li>
</ul>\n
</div>\n";
}



/*=================================================================
Science@Exeter
=================================================================*/

$mysqli = new mysqli("mysql.ex.ac.uk", "blackW1dow", "S4me:15wil4", "scienceAtExeter");
$admin_user="";
$stmt=$mysqli->prepare("
SELECT 'admin' FROM administrators WHERE ad_user=?
UNION
SELECT  'amb' FROM ambassadors WHERE a_username=?
LIMIT 1");
$stmt->bind_param("ss", $admin, $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['scienceE_admin']=true;
    echo "<div class=\"box\">
<div class=\"plus\">+</div>
<h2><a href=\"scienceexeter/\">Science@Exeter</a></h2>\n
<ul>\n";
    if ($admin_user=='admin') {
        echo "
<li><a href=\"scienceexeter/editambassadors/\">Add or Edit Ambassadors</a></li>\n
<li><a href=\"scienceexeter/checkcoverage/\">Check coverage</a></li>\n
<li><a href=\"scienceexeter/listallquestions/\">List all questions</a></li>\n";
    }
    echo "<li><a href=\"scienceexeter/listquestions/\">List questions (logged in user)</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Staff profile lists
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "eXp.user", "c.Z1@4xf", "eXp");
$admin_user="";
$stmt=$mysqli->prepare("SELECT username FROM live_superuser WHERE username=?");
$stmt->bind_param("s", $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['profiles_admin']=true;
    echo "<div class=\"box\">
<h2>Staff Profile lists</h2>\n
<ul>\n<li><a href=\"profilelists/\">Edit lists generated by user name</a></li>
</ul>\n
</div>\n";
}

/*=================================================================
Sports Matching
=================================================================*/

if ($_SESSION['sports_matching']) {
    $count++;

    echo "<div class=\"box\">
<h2>Sports Matching</h2>\n
<ul>\n
<li><a href=\"/staff/manage/sportsmatching/editmembers/\">Edit members</a></li>\n
<li><a href=\"/staff/manage/sportsmatching/editsportslist/\">Edit sports list activities</a></li>\n
<li><a href=\"/staff/manage/sportsmatching/editmemberssports/\">Edit Members Sports/Activities</a></li>\n
<li><a href=\"/staff/manage/sportsmatching/editmembersavail/\">Edit Members Availability</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
T4 users
=================================================================*/

$web_team=array('hel201','rwfm201','serwilli','mwilliam','amm216','jlh222','jr353','ec270','agb205','jsm207','mcd208','jmt212','jaaylwar', 'jt381');

if (in_array($admin, $web_team)) {
    $count++;

    echo "<div class=\"box\">
<h2><a href=\"http://www.exeter.ac.uk/staff/manage/t4users/\">T4 users</a></h2>\n
<ul>\n
<li><a href=\"http://www.exeter.ac.uk/staff/manage/t4users/\">List T4 users</a></li>
</ul>
</div>\n";
}


/*=================================================================
Studentships application
=================================================================*/
$mysqli = new mysqli("mysql.ex.ac.uk", "cCl0ggs", "m3nS4r@ex4", "xScholarships");
$admin_user="";
$stmt=$mysqli->prepare("
SELECT DISTINCT username FROM administrators WHERE username=?
UNION
SELECT username FROM studentships_admin WHERE username=? ");
$stmt->bind_param("ss", $admin, $admin);
$stmt->execute();
$stmt->bind_result($admin_user);
$stmt->fetch();
$stmt->close();

if ($admin_user) {
    $count++;
    $_SESSION['studentships_admin']=true;
    echo "<div class=\"box\">
<h2>Studentships application system</h2>\n
<ul >\n
<li><a href=\"studentshipapplications/?year=2016\">List/View 2016/7 applications</a></li>
<!--
<li><a id=\"st_link\" href=\"http://www.exeter.ac.uk/codebox/studentships/fundingadmin/stu_xml_listing.php?year=2016\" title=\"may take a few moments\" onclick=\"studentship_download(this)\" >Just download 20164/17 spreadsheet</a></li>\n
-->
<li><a href=\"studentshipapplications/editing/?year=2016\">Applications 2016/7<br/>(Not yet submitted)</a></li>\n
</ul>\n
</div>\n";
}


/*=================================================================
Supplier search tools
=================================================================*/

if ($_SESSION['suppliers']) {
    $count++;
    echo "
<div class=\"box\">
<div class=\"plus\">+</div>
<h2><a href=\"suppliers/\">Supplier search tools</a></h2>\n
<ul>\n
<li><a href=\"suppliers/managecategories/\">Manage categories</a></li>
<li><a href=\"suppliers/listsuppliers/\">List/edit suppliers</a></li>
<li><a href=\"managecommodities/\">Manage commodities</a></li>
</ul>\n
</div>\n";
}



/*==================================
Message if nothing available
===================================*/

if ($count==0) {
    echo "<p>You don't currently have any  access to anything that would be listed here.</p><p>If you think this is an error, please contact the system administrator</p>";
}

echo '</body></html>';

function LDAP_searcha($search)
{
    $host='ldaps://ldap.ex.ac.uk';
    $port=636;
    $basedn='ou=people,dc=exeter,dc=ac,dc=uk';
    $ldapconfig['host'] = $host;
    $ldapconfig['port'] = $port;
    $ldapconfig['basedn'] = $basedn;
    $ds= ldap_connect($ldapconfig['host'], $ldapconfig['port']);
    $r = ldap_search($ds, $ldapconfig['basedn'], $search);
    $ldap_entries=ldap_get_entries($ds, $r);
    $names=preg_split("/ /", $ldap_entries[0]['cn'][0]);
    $ldap->name=$names[0];
    $ldap->surname=$names[1];
    $ldap->fullname=$names[0]." ".$names[1];
    $ldap->status1=$ldap_entries[0]['exeterstatus'][0];
    $ldap->tel=$ldap_entries[0]['telephonenumber'][0];
    $ldap->email=$ldap_entries[0]['mail'][0];
    $ldap->dept=$ldap_entries[0]['exeterprimaryorg'][0];
    $ldap->username=$ldap_entries[0]['uid'][0];
    $ldap->found=$ldap_entries["count"];
    $ldap->all=$ldap_entries;
    return $ldap;
}


function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function print_header()
{
    global  $page_doctype,$page_meta,$page_styles,$my_js,$page_js,$page_title,$page_body_start,$page_top_nav,$breadcrumb,$page_content_top,$page_left_nav,$page_pre_rightnav;

    $page_styles.=<<<EOF
<style>
div#contentmainnav{margin-left:0px}
.box h2 {
font-family:Georgia, Arial, Helvetica, sans-serif;
font-size:0.9em;
color:#ffffff;
background-image:url(/media/universityofexeter/webteam/styleassets/images/corptop.gif);
background-position:left top;
background-repeat:no-repeat;
padding:0; margin:0;
padding:3px 5px 5px 10px;
font-weight:bold;
background-color:#005DAB; }
.box h2 a { color:#ffffff; }
.box h2 a:link, .box h2 a:visited { text-decoration:none; color:#ffffff; }
.box h2 a:hover, .box h2 a:active { text-decoration:underline; color:#ffffff; }
</style>
EOF;

    $page_js.=<<<EOF
<script type="text/javascript" language="javascript">

function studentship_download(obj){
obj.innerHTML='..downloading <img src="/codebox/icons/wait30.gif" width="30" height="30" alt="" />';
var t=setTimeout("restore_text()",18000);
}

function restore_text(){
var obj=document.getElementById('st_link');
obj.innerHTML='Just download spreadsheet';
return;
}

</script>
EOF;

    echo $page_doctype.$page_meta.$page_styles.$my_js.$page_js.$page_title.$page_body_start.$page_top_nav.$breadcrumb.$page_content_top.$page_left_nav.$page_pre_rightnav;
}



?>

