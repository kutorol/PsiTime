# System of the accounting of time for projects

  - *Русская версия [Readme.md][RReadme]*

In this project I tried to make similarity of Jira, only everything consists completely of mine "crutches and bicycles". That is in a box:

  - Registration with authorization
  - 2 languages: Russian and English
  - Adding, deleting, editing tasks/projects
  - Adding/deleting in/from the project of users (thus you will be able to set on them tasks and to trace their statistics)
  - Search of tasks in the phrase or in an id (if to enter digit)
  - The filter of tasks (according to the status, a priority, complexity and the performer)
  - Saving/deleting filters of tasks (as if works, but it will be debugged still)
  - Viewing of number of tasks with different sortings (in diagrams)
  - Opportunity to see a number of hours which was spent by the specific user for the specific project (to which you are attributed or which you created)
  - Opportunity to change the profile, including change of an avatar
  - Practically all functionality works through ajax

The site is developed on CodeIgniter 2.2.4. Bootstrap v3.3.5 (MIT license). Diagrams are built by means of Highcharts JS v4.1.9 library ([License][HClicense]). Loading of files uses jQuery File Upload Plugin 5.40.1 (MIT license)


> For **COMMERCIAL** use of diagrams from Highcharts 
> it is necessary [to buy their license][shopHC]

### Version
**1.0.1**.024ade1 b

### Technical requirements
PHP version 5.4 or newer is recommended.

It should work on 5.2.4 as well, but we strongly advise you NOT to run such old versions of PHP, because of potential security and performance issues, as well as missing features.

### Installation

  - Unpack archive on the local (test) server.
  - Read and fill empty constants in the file ./config.php
  - In the file ./index.php:
  ```sh
	Replace:
	define('ENVIRONMENT', 'development');
	
	On:
	define('ENVIRONMENT', 'production');
  ```
  - In the ./application/config/database.php instead of TRUE, deliver FALSE in a line: (to disconnect error output on the screen, connected to mysql)
  ```sh
	$db['default']['db_debug'] = TRUE;
  ```
  - When you fill all constants in config.php, then it is possible to launch the site.
  - If at you the database is initially not created, application will create it automatically, also all tables necessary for operation of application, will be created automatically (it is possible to view structure of tables in ./application/migrations/001_start_db.php)
  - Now you can be authorized those data which entered in ./config.php
  - If you don't need Develbar, disconnect it in ./application/config/hooks.php. Delete:
  ```sh
	$hook['display_override'][] = array(
		'class'    => 'Develbar',
		'function'     => 'debug',
		'filename'     => 'Develbar.php',
		'filepath'     => 'third_party/DevelBar/hooks'
	);
  ```
  - If not to be pleasant to you the translation of words, correct it in files:
  ```sh
	./application/libraries/language/lang_controller.php
	./application/language/db_hook/
	./application/language/russian/
  ```

### Development

If you were interested by this project, perhaps you want to develop it together with me! I will be glad to cooperate with you! :)

### Todos

 - To process a little "Saving filters"
 - To expand statistics in the form of diagrams
 - Private messages between users
 - Redesign

### License
MIT [License][MITlicenseEn]

[//]: #
   [RReadme]: <http://english.version.com/readme.md>
   [HClicense]: <http://creativecommons.org/licenses/by-nc/3.0/>
   [shopHC]: <http://shop.highsoft.com/highcharts.html>
   [MITlicenseEn]: <https://opensource.org/licenses/MIT>
 