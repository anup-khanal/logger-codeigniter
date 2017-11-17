# logger-codeigniter
Logger for CodeIgniter

### @author package modified by Anup Khanal - www.anupkhanal.info, 2017

Original @author Dan Hulton <dan@danhulton.com> - Firestick

@package CILogs_package

@copyright Copyright 2017, Anup Khanal - www.anupkhanal.info

CodeIgniter Logs is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. CodeIgniter Logs is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

# Firestick:

Add-on for CodeIgniter that makes recording historical performance easy.

Performance measuring and logging is an important part of application maintenance and improvement. FireStick makes it easy to record page render times, MySQL call times, and other related information that you can use to track down performance issues.

FireStick relies on CodeIgniter's built-in performance and benchmarking capabilities and a post-system hook. When the hook is called, FireStick logs all this information to the database for review at a later date.

Logs are split up into multiple tables based on date, with each new day's table created automatically on the first request of each new day. All logs are added using INSERT DELAYED so as to minimize impact on the database server.

Please ensure you use the .zip linked on the Project Home to install. The code in the Source tab is the full testing/development install.

# Extracting the source

Simply extract the zip file into your /application directory. The files are already in the appropriate subfolders.

# Creating the template table

Run the "logs.sql" script under db folder as a user with table create privileges. You may alter the table name (default "ci_logs") if you wish to store your performance logs in a different table.

or simply run the following SQL command:

```mysql
CREATE TABLE ci_logs (
    ip                      INT NOT NULL,
    page                    VARCHAR(255) NOT NULL,
    user_agent              VARCHAR(255) NOT NULL,
    referrer                VARCHAR(255) NOT NULL,
    logged                  TIMESTAMP NOT NULL
                            default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    username                VARCHAR(255) NOT NULL,
    memory                  INT UNSIGNED NOT NULL,
    render_elapsed          FLOAT NOT NULL,
    ci_elapsed              FLOAT NOT NULL,
    controller_elapsed      FLOAT NOT NULL,
    mysql_elapsed           FLOAT NOT NULL,
    mysql_count_queries     TINYINT UNSIGNED NOT NULL,
    mysql_queries           TEXT NOT NULL
) ENGINE=ARCHIVE;
```

Make sure to grant all privileges on this database to your database users for your application.

### Configuration

Open /application/config/firestick.php and alter to your heart's content. If you changed the database name when you created the template table, ensure you change "db_name" now.

OR use existing config and add:

```php
/*
|--------------------------------------------------------------------------
| Log Frequency Settings
|--------------------------------------------------------------------------
|
| Determines how often benchmarks are logged.  100 = 100% = all the time.
*/
$config['log_frequency'] = 100;

```

Locate and change 
```php
$config['enable_hooks'] = FALSE; 
```
in your config/config.php to 
```php
$config['enable_hooks'] = TRUE;
```

Add the following to your config/hooks.php: 
```php
$hook['post_controller_constructor'][] = array(
                                'class'    => 'CILogger',
                                'function' => 'pre_application',
                                'filename' => 'CILogger.php',
                                'filepath' => 'libraries'
                                );
$hook['post_controller'][] = array(
                                'class'    => 'CILogger',
                                'function' => 'post_application',
                                'filename' => 'CILogger.php',
                                'filepath' => 'libraries'
                                );

$hook['post_system'][] = array(
                                'class'    => 'CILogger',
                                'function' => 'resolve_profiling',
                                'filename' => 'CILogger.php',
                                'filepath' => 'libraries'
                            );
```

Add the following to your config/database.php: 

```php
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
```

Ensure that username and password are both set, and the username in question has full access to the logs database.
