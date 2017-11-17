Please ensure you use the .zip linked on the Project Home to install. The code in the Source tab is the full testing/development install.

Extracting the source

Simply extract the zip file into your /application directory. The files are already in the appropriate subfolders.

Creating the template table

Run the "firestick-create.sql" script as a user with schema create privileges. You may alter the database name (default "logs") if you wish to store your performance logs in a different database.

Make sure to grant all privileges on this database to your database users for your application.

Configuration

* Open /application/config/firestick.php and alter to your heart's content. If you changed the database name when you created the template table, ensure you change "db_name" now.
* Locate and change "$config['enable_hooks'] = FALSE;" in your config/config.php to "$config['enable_hooks'] = true;"

* Add the following to your config/hooks.php:

$hook['post_controller_constructor'][] = array( 'class' => 'Firestick', 'function' => 'pre_application', 'filename' => 'Firestick.php', 'filepath' => 'libraries' );
$hook['post_controller'][] = array( 'class' => 'Firestick', 'function' => 'post_application', 'filename' => 'Firestick.php', 'filepath' => 'libraries' );
$hook['post_system'][] = array( 'class' => 'Firestick', 'function' => 'resolve_profiling', 'filename' => 'Firestick.php', 'filepath' => 'libraries' );

* Add the following to your config/database.php:

$db['logs']['hostname'] = "localhost";
$db['logs']['username'] = "";
$db['logs']['password'] = "";
$db['logs']['database'] = "logs";
$db['logs']['dbdriver'] = "mysql";
$db['logs']['dbprefix'] = "";
$db['logs']['pconnect'] = TRUE;
$db['logs']['db_debug'] = TRUE;
$db['logs']['cache_on'] = FALSE;
$db['logs']['cachedir'] = "";
$db['logs']['char_set'] = "utf8";
$db['logs']['dbcollat'] = "utf8_general_ci";

* Ensure that username and password are both set, and the username in question has full access to the logs database.
