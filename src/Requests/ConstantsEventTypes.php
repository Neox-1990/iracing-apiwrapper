<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Returns all event types (Practice, Qualify, Race, ...) and their ids
 */
class ConstantsEventTypes extends Request
{
    /**
     * Get data as JSON String
     *
     * @return String Data in JSON format
     */
    public function getJSON()
    :String
    {
        $json = $this->perform("https://members-ng.iracing.com/data/constants/event_types", [])->getResponse();
        //$json = file_get_contents($json->link);
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
