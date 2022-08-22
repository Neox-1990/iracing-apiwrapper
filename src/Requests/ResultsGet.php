<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Get the results of a subsession, if authorized to view them. 
 * series_logo image paths are relative to https://images-static.iracing.com/img/logos/series/
 */
class ResultsGet extends Request
{
    /**
     * Get data as JSON String
     *
     * @param  integer $subsession_id ID of the Session
     * @param  boolean $include_licenses add license info to the driver info
     * @return String
     */
    public function getJSON(int $subsession_id = 0, bool $include_licenses = false)
    :String
    {
        //clamp quarter to valid values
        $parameter = [
            'subsession_id' => $subsession_id,
            'include_licenses' => $include_licenses
        ];
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/results/get", $parameter)->getResponse());
        $json = file_get_contents($json->link);
        return $json;
    }

    /**
     * Get data as associative array
     *
     * @param  integer $subsession_id ID of the Session
     * @param  boolean $include_licenses add license info to the driver info
     * @return array Data in associative array
     */
    public function getArray(int $subsession_id = 0, bool $include_licenses = false)
    :array
    {
        return json_decode($this->getJSON($subsession_id, $include_licenses), true);
    }
}
