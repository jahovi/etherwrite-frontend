<?php

// Required strings.
$string['modulename'] = 'Write';
$string['modulenameplural'] = 'Writing';
$string['modulename_help'] = 'Write Plugin';
$string['pluginadministration'] = 'Administrator';
$string['pluginname'] = 'Write';

// Time
$string['days'] = 'd';
$string['hours'] = 'hr';
$string['minutes'] = 'min';

// Errors
$string['unknown_error'] = 'An unknown error has occurred.';
$string['eva_not_available'] = 'The EtherWrite data analytics node is not available. If the problem persist, please contact your administrator. Tell them EVA does not respond.';
$string['eva_is_back'] = 'The EtherWrite data analytics node is back online.';

// Settings
$string['serverurl'] = 'Etherpad Server URL.';
$string['serverurldescription'] = 'The server URL of the Etherpad instance.';
$string['apikey'] = 'Etherpad API Key';
$string['apikeydescription'] = 'The API key for the Etherpad instance.';
$string['localinstallation'] = 'Local installation';
$string['localinstallationdescription'] = 'Select this option if the installation is located locally on your computer.';
$string['nogroupselect'] = "(Select grouping)";
$string['evaurl'] = 'EVA Server URL';
$string['evaurldescription'] = 'URL to the EVA instance for data analytics.';
$string['evasecret'] = 'EVA API-Key';
$string['evasecretdescription'] = 'Key for the data exchange between Moodle and EVA. The same key has to be configured for the EVA instance.';

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
$string['novalidevakey'] = 'Invalid EVA key';
$string['novalidevaurl'] = 'Invalid EVA URL';

$string['legend'] = 'Legend';
$string['legend.click-to-highlight'] = 'Click to highlight';

// Dashboard
$string['dashboard.nothingToSee.title'] = "Nothing to see here?";
$string['dashboard.nothingToSee.cta'] = "Just click this button to add widgets from the catalogue";
$string['dashboard.move'] = "Move widgets with";

// Widget categories
$string['widgets.category.collaboration'] = 'Collaboration';
$string['widgets.category.timecourse'] = 'Time course';
$string['widgets.category.activity'] = 'Activity';

$string['widgets.groupParticipants'] = "Project participants";
$string['widgets.groupParticipants.group'] = "Group:";

$string['widgets.cohesionDiagram.title'] = "Cohesion (Interaction between participants)";

// Countdown
$string['kpi-countdown-text'] = 'Time until deadline:';
$string['kpi-countdown-no-deadline'] = 'none';

// Document Metrics
$string['document-metrics-chars'] = '# Characters:';
$string['document-metrics-words'] = '# Words:';

// Widgets Info-Texts
$string['widget-info-authoringRatios_bar'] = 'Shows the proportion of the current text that has been written by each author';

$string['widget-info-authoringRatios_pie'] = 'Shows the proportion of the current text that has been written by each author';

$string['widget-info-participation_diagram'] = 'This chart shows attendee participation for individual hours or days.
If the work is still very short, the participation is displayed for hours.
For work that has been going on for some time, participation is displayed daily.
The users are displayed in their respective color on the bar. For an hour / a day, where no work has been done, no bar exists. Otherwise, the total height of a bar is always 1.0 and refers to the work done in one hour / one day.';

$string['widget-info-operations_bar'] = 'This chart shows the number of operations performed.
Participants will see their own performed operations and the average operations performed by all other participants.
For moderators, the operations performed for all participants are displayed.
Operations are: write, edit, copy and delete.';

$string['widget-info-activityOverTime'] = 'Shows the amount of activity per user over time. Activities are editing, writing, pasting from clipboard and deleting of text. If less than three days have passed since the beginning of the assignment, the activities will be grouped by hour, later by day.';

$string['widget-info-cohesionDiagram'] = 'Shows the cohesion between the participants of the group. The distance between two circles represents the frequency in which the two participants worked together on the text. 
Directional arrows represent collaboration on the text of others. An arrow from A to B indicates that participant A added to or edited B\'s text. The thickness of the arrow shows the intensity of the collaboration.
<em><strong>Note:</strong> By clicking on the legend an individual participant can be highlighted.</em>';

$string['widget-info-etherViz'] = 'The EtherViz diagram gives you an overview of the text creation over time. The y-axis shows the number of characters in the text at a given date. The x-axis shows you as many revisions as the project has days.';

// Dashboard
$string['your-dashboard'] = 'Your Dashboard';

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

// DocumentMetrics widget
$string['documentMetricsWidgetTitle'] = 'Document Metrics';
$string['documentMetricsWidgetNumChars'] = 'Characters';
$string['documentMetricsWidgetNumWords'] = 'Words';