<?php

// Required strings.
$string['modulename'] = 'Write';
$string['modulenameplural'] = 'Writing';
$string['modulename_help'] = 'Write Plugin';
$string['pluginadministration'] = 'Administrator';
$string['pluginname'] = 'Write';

// Unknown  error
$string['unknown_error'] = 'An unknown error has occurred.';

// Settings
$string['serverurl'] = 'Etherpad Server URL.';
$string['serverurldescription'] = 'The server URL of the Etherpad instance.';
$string['apikey'] = 'Etherpad API Key';
$string['apikeydescription'] = 'The API key for the Etherpad instance.';
$string['localinstallation'] = 'Local installation';
$string['localinstallationdescription'] = 'Select this option if the installation is located locally on your computer.';
$string['nogroupselect'] = "(Select grouping)";

// CM
$string['grouping'] = 'Grouping';

// Vue
$string['loadeditor'] = 'Tries to load the editor...';
$string['loading'] = 'Loading..';
$string['leftclick'] = "Left click";

// Webservices
$string['novalidepurl'] = 'Invalid Etherpad URL.';
$string['novalidapikey'] = 'Invalid Etherpad API key.';
$string['nogrouping'] = 'No grouping specified for this task.';
$string['nogroup'] = 'User does not belong to a group of the grouping.';
$string['couldnotcreateepgroup'] = 'Could not create Etherpad group.';
$string['couldnotfetchgrouppads'] = 'Could not fetch pads of Etherpad group.';
$string['couldnotcreatepad'] = 'Could not create Etherpad pad.';
$string['couldnotcreateauthor'] = 'Could not create Etherpad author.';
$string['couldnotcreatesession'] = 'Could not create Etherpad session.';

$string['legend'] = 'Legend';
$string['legend.click-to-highlight'] = 'Click to highlight';

// Dashboard
$string['dashboard.nothingToSee.title'] = "Nothing to see here?";
$string['dashboard.nothingToSee.cta'] = "Just click this button to add widgets from the catalogue";
$string['dashboard.move'] = "Move widgets with";

// Widget categories
$string['widgets.category.barchart'] = 'Bar chart';
$string['widgets.category.piechart'] = 'Pie chart';
$string['widgets.category.linechart'] = 'Line chart';
$string['widgets.category.other'] = 'Other';

$string['widgets.groupParticipants'] = "Project participants";
$string['widgets.groupParticipants.group'] = "Group";

$string['widgets.cohesionDiagram.title'] = "Cohesion (Interaction between participants)";

// KPIs
$string['kpi-countdown-text'] = 'Time till deadline: ';
$string['kpi-countdown-no-deadline'] = 'no deadline';

// Widgets Info-Texts
$string['widget-info-authoringRatios_bar'] = 'Description of of author ratios barchart.';

$string['widget-info-authoringRatios_pie'] = 'Description of author ratios piechart.';

$string['widget-info-participation_diagram'] = 'This chart shows attendee participation for individual hours or days.
If the work is still very short, the participation is displayed for hours.
For work that has been going on for some time, participation is displayed daily.
The users are displayed in their respective color on the bar. For an hour / a day, where no work has been done, no bar exists. Otherwise, the total height of a bar is always 1.0 and refers to the work done in one hour / one day.';

$string['widget-info-groupParticipants'] = 'Description of group participation chart.';

$string['widget-info-operations_bar'] = 'This chart shows the number of operations performed.
Participants will see their own performed operations and the average operations performed by all other participants.
For moderators, the operations performed for all participants are displayed.
Operations are: write, edit, copy and delete.';

$string['widget-info-activityOverTime'] = 'Shows the amount of changes per user over time. If less than three days have passed since the beginning of the assignment, the activities will be grouped by hour, later by day.';

$string['widget-info-cohesionDiagram'] = 'Shows the cohesion between the participants of the group. The distance between two circles represents the frequency in which the two participants worked together in the text. 
Directional arrows represent collaboration on the text of others. An arrow from A to B indicates that participant(s) A added to or edited B\'s text. The thickness of the arrow shows the intensity of the collaboration.
<em><strong>Note:</strong> With click on the legend an individual participant can be highlighted.</em>';

$string['widget-info-etherViz'] = 'The EtherViz diagram gives you an overview of the text creation over time. The y-axis shows the number of characters in the text at a given date. The x-axis shows you as many revisions as the project has days.';

// Operations widget
$string['operationswidgettitle'] = 'Operations performed';
$string['operationswidgetedit'] = 'Edit';
$string['operationswidgetwrite'] = 'Write';
$string['operationswidgetpaste'] = 'Paste';
$string['operationswidgetdelete'] = 'Delete';
$string['operationswidgetaverage'] = 'Average';
$string['operationswidgetxaxisteacher'] = 'Participant';
$string['operationswidgetxaxisstudent'] = 'Operations';
$string['operationswidgetyaxis'] = 'Count';

// Participation Widget
$string['participationwidgettitle'] = 'Participation';
$string['participationwidgetyaxis'] = 'Participation';
$string['participationwidgetxaxis'] = 'Date';
// ActivityOverTime Widget
$string['activityovertimewidgettitle'] = 'Activity over time';
$string['activityovertimewidgetyaxis'] = 'Activity';
$string['activityovertimewidgetxaxis'] = 'Time';
$string['activityovertimewidgetscalinglinear'] = 'Linear';
$string['activityovertimewidgetscalinglogarithmic'] = 'Logarithmic';