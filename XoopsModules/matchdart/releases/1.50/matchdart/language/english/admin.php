<?php

//Topbar
define("_AM_XD_NAVSEASONS","Seasons");
define("_AM_XD_NAVEVENTS","Events");
define("_AM_XD_NAVPLAYERS","Players");
define("_AM_XD_NAVEVENTMATCHES","Event matches");
define("_AM_XD_NAVEVENTTABLE","Event table");
define("_AM_XD_NAVPREFERENCES","Preferences");
define("_AM_XD_NAVPERMISSIONS","Permissions");
define("_AM_XD_NAVUPDATE","Update");
define("_AM_XD_NAVABOUT","About");

//Head
define("_AM_XD_CHOSEASON","Please choose season:");
define("_AM_XD_SEASONGO","Go");
define("_AM_XD_SEASELECT","Selected season: ");
define("_AM_XD_SEASELDROP","You may change season by selecting new season from dropdown menu: ");
define("_AM_XD_CHOEVENT","Please choose event:");
define("_AM_XD_EVENTSELECT","Selected event: ");
define("_AM_XD_EVENTSELDROP","You may change event by selecting new event from dropdown menu: ");
define("_AM_XD_EVENTGO","Go");

//Seasons management (seasons.php)
define("_AM_XD_ADDSEASON","Add new season");
define("_AM_XD_SEASONNAMEYEARS","Season name (years)");
define("_AM_XD_SEASONADD","Add season");
define("_AM_XD_SEASONSAVAILABLE","Seasons in database");
define("_AM_XD_SEASONNOTE","NP = This season is not published yet.");
define("_AM_XD_SEASONMODIFYDELETE","Modify / delete season");
define("_AM_XD_DEFAULTSEASON","Default season?");
define("_AM_XD_SEASONPUBLISHED","Published:");
define("_AM_XD_SEASONMODIFY","Modify  season");
define("_AM_XD_SEASONDELETE","Delete season");
define("_AM_XD_NOSEASONS","No seasons so far in database");
define("_AM_XD_SEASONNP","(NP)");
define("_AM_XD_SEASONDUPLICATE","<br>There is already a season with this name in database.<br>Please write another name for the season");
define("_AM_XD_SEASONHASMATCHES","<br>There is already a match booked for the season you wanted to delete.<br> You must delete match first.");

//Events (events.php)
define("_AM_XD_ADDEVENT","Add new event");
define("_AM_XD_EVENTNAMEYEARS","Event name");
define("_AM_XD_EVENTPUBLISHED","Published:");
define("_AM_XD_EVENTPOINTSWIN","Points for Win");
define("_AM_XD_EVENTPOINTSDRAW","Points for Draw");
define("_AM_XD_EVENTPOINTSLOSS","Points for Loss"); 
define("_AM_XD_EVENTDRAWLINE","Draw line after which position<br>If many, separate by commas");
define("_AM_XD_DEFAULTEVENT","Default event?");
define("_AM_XD_EVENTADD","Add event");
define("_AM_XD_EVENTSAVAILABLE","Events in database");
define("_AM_XD_EVENTNOTE","NP = This event is not published yet.");
define("_AM_XD_EVENTNP","(NP)");
define("_AM_XD_NOEVENTS","No events so far in database");
define("_AM_XD_EVENTMODIFYDELETE","Modify / delete event");
define("_AM_XD_EVENTMODIFY","Modify event");
define("_AM_XD_EVENTDELETE","Delete event");
define("_AM_XD_EVENTDUPLICATE","<br>There is already a event with this name in database.<br>Please write another name for the event");
define("_AM_XD_EVENTHASMATCHES","<br>There is already a match booked for the event you wanted to delete.<br> You must delete match first.");
define("_AM_XD_EVENTPLAYERSTATS","Use extended Player Stats:");

//Players management (players.php)
define("_AM_XD_PLAYERSAVAILABLE","Players in database");
define("_AM_XD_NOPLAYERSAVAILABLE","No players in database");
define("_AM_XD_ADDNEWPLAYER","Add new player");
define("_AM_XD_PLAYERNAME","Player name:");
define("_AM_XD_PLAYERISYOURS","Is your player?");
define("_AM_XD_ADDPLAYER","Add player");
define("_AM_XD_PLAYERMODIFY","Modify  player");
define("_AM_XD_PLAYERDELETE","Delete player");
define("_AM_XD_PLAYERMODIFYDELETE","Modify / delete player");
define("_AM_XD_PLAYERDUPLICATE","<br>There is already a player with this name in database.<br>Please write another name for this player");
define("_AM_XD_PLAYERISINUSE","<br>Permission to delete is denied!<br>Player is already in use.<br>Push back button to get back");
define("_AM_XD_PLAYERXUID","Player assigned to Xoops User");
define("_AM_XD_PLAYERNOXUID","None");

//Matches management(eventmatches.php)
define("_AM_XD_ADDTWOPLAYERS","Add at least two players into the database");
define("_AM_XD_ADDPLAYERS","Add players");
define("_AM_XD_ADDMATCH","Add match");
define("_AM_XD_ADDMATCHES","Add matches");
define("_AM_XD_ADDMATCHNOTE","If you can't find a specific player, check that it is available in opponents.");
define("_AM_XD_ADDMATCHNOTE2","Add as much matches as you want, max 15 per one time. <br>Matches with sets filled in the form are added to the database.");
define("_AM_XD_DATE","Date:");
define("_AM_XD_HOMEPLAYER","Home player:");
define("_AM_XD_AWAYPLAYER","Away player:");
define("_AM_XD_SETS","Sets");
define("_AM_XD_SETSHOME","LH");
define("_AM_XD_SETSAWAY","LA");
define("_AM_XD_BONUSPOINTS","BP");
define("_AM_XD_BONUSHOME","BP");
define("_AM_XD_BONUSAWAY","BP");
define("_AM_XD_HIGHFINISH","HiFi");
define("_AM_XD_HIGHFINISHHOME","HFH");
define("_AM_XD_HIGHFINISHAWAY","HFA");
define("_AM_XD_MODMATCHES","Modify matches");
define("_AM_XD_MODMATCH","Modify match");
define("_AM_XD_DATETIME","Date and time");
define("_AM_XD_MODNOTICE1","You can't change home or away player in this mode. Click the match to modify home/away player.");
define("_AM_XD_MODINPUT","Click here to modify the matches");
define("_AM_XD_MODINPUT2","Click here to modify the match");
define("_AM_XD_DELINPUT","Delete (can't be undone)");
define("_AM_XD_NOMATCHESYET","No matches yet in season");
define("_AM_XD_MATCHESYET","Matches in season");
define("_AM_XD_NOEVENTMATCHESYET","No matches yet in event");
define("_AM_XD_EVENTMATCHESYET","Matches in league");
define("_AM_XD_180","180");

//Permissions (permissions.php)
define("_AM_XD_PERMISSION","Matchdart Permissions");
define("_AM_XD_SUBMITMATCHES","Submit Matches");

//Update Module (update.php)
define("_AM_XD_MODUPDATEWARNING","ATTENTION: backup your database first before updating the module!");
define("_AM_XD_MODUPDATE","Update Module Database");
define("_AM_XD_MODNOUPDATE","No Module Database update needed.");

//About (about.php)
define("_AM_XD_ABOUT_RELEASEDATE","Release Date");
define("_AM_XD_ABOUT_AUTHOR","Author");
define("_AM_XD_ABOUT_CREDITS","Credits");
define("_AM_XD_ABOUT_README","General Info");
define("_AM_XD_ABOUT_MANUAL","Help");
define("_AM_XD_ABOUT_LICENSE","License");
define("_AM_XD_ABOUT_MODULE_STATUS","Status");
define("_AM_XD_ABOUT_WEBSITE","Web Site");
define("_AM_XD_ABOUT_AUTHOR_NAME","Author Name");
define("_AM_XD_ABOUT_AUTHOR_WORD","Author Word");
define("_AM_XD_ABOUT_CHANGELOG","Change Log");
define("_AM_XD_ABOUT_MODULE_INFO","Module Info");
define("_AM_XD_ABOUT_AUTHOR_INFO","Author Info");
define("_AM_XD_ABOUT_DISCLAIMER","Disclaimer");
define("_AM_XD_ABOUT_DISCLAIMER_TEXT","GPL Licensed - No Warranty");

?>