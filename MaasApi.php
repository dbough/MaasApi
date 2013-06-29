<?php // MaasApi.php v0.1.0
/**
 * The MaasApi class is an attempt to allow PHP developers 
 * a way to easily gather data from the {MAAS} API.
 *
 * More info on the {MAAS} API can be found at http://marsweather.ingenology.com/
 *
 * @author Dan Bough <daniel.bough@gmail.com> http://www.danielbough.com
 * @copyright Copyright (C) 2010-2013
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */

class MaasApi {
    /**
     * Used to hold alert information.  Currently the format is unknown.
     * @var
     */
    var $alerts;
    /**
     * URL to gather the latest info.
     * @var string
     */
    var $latestUrl = "http://marsweather.ingenology.com/v1/latest/";
    /**
     * JSON object containing the latest weather data.
     */
    var $report;
    /**
     * URL to gather archived info.
     * @var string
     */
    var $archiveUrl = "http://marsweather.ingenology.com/v1/archive/";
    /**
     * URL with query parameters.
     * @var string
     */
    var $archiveUrlSearch;
    /**
     * Number of pages of data returned by a request.
     * @var int
     */
    var $count;
    /**
     * URL pointing to the next page of data.
     * @var string
     */
    var $next;
    /**
     * URL pointing to the previous page of data.
     * @var string
     */
    var $previous;
    /**
     * Array containing archive data.
     */
    var $results = array();
    /**
     * Earth data 
     * @var string
     */
    var $terrestrialDate;
    /**
     * Solar date
     * @var int
     */
    var $sol;
    /**
     * Solar longitude
     * @var int
     */
    var $ls;
    /**
     * Minimum temperature in Celsius
     * @var int
     */
    var $minTemp;
    /**
     * Minimum temperature in Fahrenheit
     * @var int
     */
    var $minTempFahrenheit;
    /**
     * Max temperature in Celsius
     * @var int
     */
    var $maxTemp;
    /**
     * Max temperature in Fahrenheit
     * @var int
     */
    var $maxTempFahrenheit;
    /**
     * Atmospheric Pressure
     * @var int
     */
    var $pressure;
    /**
     * @var string
     */
    var $pressureString;
    /**
     * Absolute humidity
     */
    var $absHumidity;
    /**
     * Wind speed
     */
    var $windSpeed;
    /**
     * Wind direction
     */
    var $windDirection;
    /**
     * Atmospheric opacity
     * @var string
     */
    var $atmoOpacity;
    /**
     * Season
     * @var string
     */
    var $season;
    /**
     * Sunrise
     * @var string
     */
    var $sunrise;
    /**
     * Sunset
     * @var string
     */
    var $sunset;

    /**
     * Object containing raw json object with latest weather info.
     * @return json
     */
    public function getLatestRaw()
    {
        return file_get_contents($this->latestUrl);   
    }

    /**
     * Gets latest weather results.
     * @return array
     */
    public function getLatest()
    {
        $jsonData = file_get_contents($this->latestUrl);
        $this->report = json_decode($jsonData)->report;
        return $this->report;

    }

    /**
     * Get latest weather data as a JSON object
     * @return 
     */
    public function getLatestJson()
    {
        return json_encode($this->getLatest());

    }

    /**
     * Object containing first page of archive weather data as a JSON object
     * @return json
     */
    public function getArchiveRaw()
    {
        return file_get_contents($this->archiveUrl);
    }

    /**
     * Get all archived data.
     * @return array
     */
    public function getArchiveAll()
    {
        $jsonData = file_get_contents($this->archiveUrl);
        $data = json_decode($jsonData);
        $results = $data->results;


        // Determine how many pages of data there are
        $pages = ceil($data->count / 10);

        // Context for ignoring HTTP / PHP errors with file_get_contents
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));

        // Start at page 2 and go to the end
        for ($i=2;$i<=$pages;$i++) {
            $jsonData = file_get_contents($this->archiveUrl . "?page=" . $i, false, $context);
            
            // Need to determine if the jsonData is a valid object.
            // Seems MAAS API returns bogus data sometimes.            
            if ($this->isJson($jsonData)) {
                foreach (json_decode($jsonData)->results as $result) {
                    array_push($results, $result);
                }
            }
        }

        return $results;
    }

    /**
     * Determines whether or not a string is a JSON object
     * @param  string  $string
     * @return boolean
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}