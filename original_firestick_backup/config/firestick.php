<?php
/**
 * Main library file for Firestick.
 *
 * @author Dan Hulton <dan@danhulton.com>
 * @package firestick_config_package
 * @copyright Copyright 2008, Dan Hulton
 *
 *  This file is part of FireStick.
 *
 *  Foobar is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FireStick is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FireStick.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Log Frequency Settings
|--------------------------------------------------------------------------
|
| Determines how often benchmarks are logged.  100 = 100% = all the time.
*/

$config['log_frequency'] = 100;

/*
|--------------------------------------------------------------------------
| Log Database Settings
|--------------------------------------------------------------------------
|
| Defines the database in which all logs are stored.  You can store your
| logs in the same database as your application if you like (by changing
| db_name below and ensuring you alter the create.sql to create the
| template in your application's database), but splitting it into two
| databases is cleaner.
*/

$config['db_name'] = 'logs';

?>