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
     * URL to gather the latest info.
     * @var string
     */
    var $latestUrl = "http://marsweather.ingenology.com/v1/latest/";
    /**
     * URL to gather archived info.
     * @var string
     */
    var $archiveUrl = "http://marsweather.ingenology.com/v1/archive/";

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
     * Accepts results from an initial archive query and returns all results.
     * @param  object $data
     * @return array
     */
    public function get($data)
    {
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
            
            // If $jsonData is an object add it's data to our results array.         
            if ($this->isJson($jsonData)) {
                foreach (json_decode($jsonData)->results as $result) {
                    array_push($results, $result);
                }
            }
        }

        return $results;
    }

    /**
     * Get all archived data.
     * @return array
     */
    public function getArchiveAll()
    {
        $jsonData = file_get_contents($this->archiveUrl);
        $data = json_decode($jsonData);

        return $this->get($data);
    }

    /**
     * Get archived data between two dates.
     * @param  string $startDate
     * @param  string $endDate
     * @return array
     */
    public function getArchiveRange($startDate, $endDate)
    {
        $urlSuffix = "?terrestrial_date_start=" . urldecode($startDate) . "&terrestrial_date_end=" . urlencode($endDate);
        $jsonData = file_get_contents($this->archiveUrl . $urlSuffix);
        $data = json_decode($jsonData);

        return $this->get($data);
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