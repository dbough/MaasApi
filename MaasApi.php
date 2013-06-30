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
     * Latest data URL.
     * @var string
     */
    var $latestUrl = "http://marsweather.ingenology.com/v1/latest/";
    /**
     * Archive data URL.
     * @var string
     */
    var $archiveUrl = "http://marsweather.ingenology.com/v1/archive/";

    /**
     * Raw JSON object with latest data.
     * @return string
     */
    public function getLatestRaw()
    {
        return file_get_contents($this->latestUrl);   
    }

    /**
     * Latest data as JSON
     * @return string
     */
    public function getLatestJson()
    {
        return json_encode($this->getLatest());

    }

    /**
     * Array of latest data.
     * @return array
     */
    public function getLatest()
    {
        $jsonData = file_get_contents($this->latestUrl);
        $this->report = json_decode($jsonData)->report;
        return $this->report;
    }

    /**
     * Raw JSON object with first page of archived data.
     * @return string
     */
    public function getArchiveRaw()
    {
        return file_get_contents($this->archiveUrl);
    }

    /**
     * JSON object of all archive data.
     * @return array
     */
    public function getArchiveJson()
    {
       return json_encode($this->getArchiveAll());
    }

    /**
     * Array of all archive data.
     * @return array
     */
    public function getArchiveAll()
    {
        $jsonData = file_get_contents($this->archiveUrl);
        $data = json_decode($jsonData);

        return $this->get($data);
    }

    /**
     * Get archive data based on search parameters.
     * All properties of the object returned by the {MAAS} API can be searched for.
     * You can also provide a date range with terrestrial_date_start and terrestrial_date_end.
     * See more info at http://marsweather.ingenology.com/
     * @param  array $params
     * @return array
     */
    public function getArchiveSearch($params)
    {
        /*
            Example $params:
            $params = array(
                "terrestrial_date_start"=>"2013-05-01",
                "terrestrial_date_end"=>"2013-05-10"
            );
         */
        $urlSuffix = "?";
        $count = count($params);

        foreach ($params as $key => $val) {
            $urlSuffix .= $key . "=" . urlencode($val);

            // Unless we're at the last param, add a & separator
            if ($count != 1) {
                $urlSuffix .="&";
            }
            $count--;
        }

        $jsonData = file_get_contents($this->archiveUrl . $urlSuffix);
        $data = json_decode($jsonData);

        return $this->get($data);

    }

    /**
     * Uses first page results of archive query to gather all data from that query.
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
     * Determines whether or not a string is a JSON object
     * @param  string  $string
     * @return boolean
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}