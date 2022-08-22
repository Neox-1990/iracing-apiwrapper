<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Returns all countries with name and shorthand XX
 */
class LookupCountries extends Request
{
    /**
     * Get data as JSON String
     *
     * @return String Data in JSON format
     */
    public function getJSON()
    :String
    {
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/lookup/countries", [])->getResponse());
        $json = file_get_contents($json->link);
        return $json;
    }

    /**
     * Get data as associative array
     *
     * @return array Data in associative array
     */
    public function getArray()
    :array
    {
        return json_decode($this->getJSON(), true);
    }
}
