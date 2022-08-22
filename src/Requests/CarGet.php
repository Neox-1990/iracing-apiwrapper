<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Returns detailed car information for every car
 */
class CarGet extends Request
{
    /**
     * Get data as JSON String
     *
     * @return String Data in JSON format
     */
    public function getJSON()
    :String
    {
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/car/get", [])->getResponse());
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
