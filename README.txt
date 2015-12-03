This Elgg plugin monitors group member activity and performance for a number of indicators and shows the results in spider diagrams and bar charts.
The group member activity data is based on queries on user actions stored in the Elgg database. There are five dimensions, e.g. the number of created objects or the number of responses in Elgg discussion fora.
The group member performance is based on performance ratings that are gathered externally, using a questionnaire, and imported weekly and monthly. Group members rate themselves, other group members and the group as a whole on nine dimensions.

See the 'EVS Activity and Performance Dashboard - User Manual' for a detailed description.

After you have enabled this plugin, you have to configure it. To do this go to the Administration -> Tools Administration -> ActivityAndPerformanceDashboard Settings.

Here you can configure the following settings:
- Choose privacy setting. The default option is that you can only see your own data and the group mean (without me). The second option is that you see your own data and data of anonymized users. The thirth option is the same as the second but show users non-anonymized.
- Enter group guids to monitor, comma separated. Enter at least one group. If a user is member of multiple groups or is admin he can change the group in the plugin's edit page.
- Enter user guids for tutors. If they are part of the monitored group their activity will not be visible, but they can monitor activity of group members. Admins can monitor all groups without being a member.
- Enter user guids of other users whose activity should not be visible, e.g., experts who are group member too or banned users.
- Choose the default value for sharing data with other users. It default is No, meaning users will have to explicitly have to agree to share their data in the plugin's edit page. A user only sees data of an another user if both have agreed to share their data.
- Enter the end date of the period (max twelve months) you want to view, if empty it is the current date. It can be overruled by tutors and admins in the plugin's edit page, so they see another end date.
- Choose number of months to view, between 1 and 12.
- Enter JavaScript array with monthly group member ratings. It is based on externally gathered data. The format is rather specific.
- Enter JavaScript array with weekly group member ratings. It is based on externally gathered data. The format is rather specific.

The plugin works on Elgg version 1.7. It runs without problems on version 1.7.1 and 1.7.8.
Due to heavy dependency on the Elgg database structure it won't work correctly on higher versions of Elgg.

The plugin uses jQuery version 1.10.2 (https://jquery.com/) and D3.js version 3.5.3 (http://d3js.org/)