<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Quite frankly, i don't know, what this is good for.
 * Maybe which clubs where available at which point in the past
 */
class LookupClubHistory extends Request
{
    /**
     * Get data as JSON String
     *
     * @param  integer $season_year Year
     * @param  integer $season_quarter Quarter of the year (1-4)
     * @return String
     */
    public function getJSON(int $season_year = 0, int $season_quarter = 0)
    :String
    {
        //clamp quarter to valid values
        $season_quarter = min(max(1,$season_quarter), 4);
        $parameter = [
            'season_year' => $season_year,
            'season_quarter' => $season_quarter
        ];
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/lookup/club_history", $parameter)->getResponse());
        $json = file_get_contents($json->link);
        return $json;
    }

    /**
     * Get data as associative array
     *
     * @param  integer $season_year Year
     * @param  integer $season_quarter Quarter of the year (1-4)
     * @return array Data in associative array
     */
    public function getArray(int $season_year = 0, int $season_quarter = 0)
    :array
    {
        return json_decode($this->getJSON($season_year, $season_quarter), true);
    }
}
