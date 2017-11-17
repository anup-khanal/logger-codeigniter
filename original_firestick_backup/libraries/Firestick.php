<?php
/**
 * Main library file for Firestick.
 *
 * @author Dan Hulton <dan@danhulton.com>
 * @package firestick_package
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

class Firestick {

    /**
     * @var integer The percentage chance that any request will be logged
     */
    var $log_frequency;

    /**
     * @var object The main CodeIgniter object.
     */
    var $CI;

    /**
     * @var string The name of the database to log to.
     */
    var $db_name;

    /**
     * Constructor.
     */
    function __construct() {
        $this->CI =& get_instance();

        if (!is_null($this->CI)) {
            // Load firestick configuration
            $this->CI->config->load('firestick', true);
            $this->log_frequency = $this->CI->config->item('log_frequency', 'firestick');
            $this->db_name = $this->CI->config->item('db_name', 'firestick');
        }
    }

    /**
     * Sets a benchmark point before the application code runs.
     */
    function pre_application() {
        $this->CI->benchmark->mark('pre_controller');
    }

    /**
     * Records the total amount of time taken for the application code.
     */
    function post_application() {
        $this->CI->benchmark->mark('post_controller');
    }

    /**
     * Sets the log frequency.
     *
     * @param integer $new_frequency
     */
    function set_frequency($new_frequency) {
        assert('is_long($new_frequency)');

        $this->log_frequency = $new_frequency;
    }

    /**
     * If we are logging this run, write profiling results to DB.
     */
    function resolve_profiling() {
        // If we should log this run
        if (rand(0, 99) < $this->log_frequency) {
            // Total consumed memory
            $memory = ( ! function_exists('memory_get_usage')) ? 0 : memory_get_usage();

            // Total elapsed script time
            $render_elapsed = $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');

            // Controller elapsed script time
            $controller_elapsed = $this->CI->benchmark->elapsed_time('pre_controller', 'post_controller');

            // CodeIgniter elapsed script time
            $ci_elapsed = $render_elapsed - $controller_elapsed;

            // DB calls
            if ( ! class_exists('CI_DB_driver')) {
                $mysql_count_queries    = 0;
                $mysql_queries          = '';
                $mysql_elapsed          = 0;
            }
            else {
                if (count($this->CI->db->queries) == 0) {
                    $mysql_count_queries    = 0;
                    $mysql_queries          = '';
                    $mysql_elapsed          = 0;
                }
                else {
                    $query_accum = 0;
                    $aq = array();
                    $first = true;

                    foreach ($this->CI->db->queries as $key => $val) {
                        // Get and accumulate time for this query
                        $time = number_format($this->CI->db->query_times[$key], 4);
                        $query_accum += $this->CI->db->query_times[$key];

                        // Get and accumulate actual queries
                        $val = str_replace("\n" , ' ', htmlspecialchars($val, ENT_QUOTES));
                        $aq[] = $first ? 'Query: ' : "\nQuery: ";
                        $aq[] = $val;
                        $aq[] = "\nTime:  ";
                        $aq[] = $time;

                        $first = false;
                    }

                    $mysql_count_queries    = count($this->CI->db->queries);
                    $mysql_queries          = implode('', $aq);
                    $mysql_elapsed          = number_format($query_accum, 4);
                }
            }
            // Prepare insert
            $this->CI->load->database('logs');
            $table_name = $this->db_name . '.performance_log_' . @date('ymd');
            $sql = "INSERT DELAYED INTO $table_name (
                    ip,
                    page,
                    user_agent,
                    referrer,
                    memory,
                    render_elapsed,
                    ci_elapsed,
                    controller_elapsed,
                    mysql_count_queries,
                    mysql_queries,
                    mysql_elapsed
                ) VALUES (
                    '" . ip2long(@$_SERVER['REMOTE_ADDR']) . "',
                    '" . @$_SERVER['REQUEST_URI'] . "',
                    '" . @$_SERVER['HTTP_USER_AGENT'] . "',
                    '" . @$_SERVER['HTTP_REFERER'] . "',
                    '$memory',
                    '$render_elapsed',
                    '$ci_elapsed',
                    '$controller_elapsed',
                    '$mysql_count_queries',
                    '$mysql_queries',
                    '$mysql_elapsed'
                );";

            // Turn off DB debug mode
            $saved_debug = $this->CI->db->db_debug;
            $this->CI->db->db_debug = false;

            // Attempt query
            $this->CI->db->query($sql);

            // Restore DB debug mode
            $this->CI->db->db_debug = $saved_debug;

            // Handle "Table Not Found" error
            if (1146 === $this->CI->db->_error_number()) {
                // Create new table for each new day
                $this->CI->db->query('CREATE TABLE ' . $table_name . ' LIKE ' . $this->db_name . '.firestick_log_template');

                // Insert again
                $this->CI->db->query($sql);
            }
        }
    }
}

?>
