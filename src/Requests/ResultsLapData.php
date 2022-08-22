<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Get the eventlog of a subsession, if authorized to view them. 
 * The data comes in chunks and will be added to the return by default
 */
class ResultsLapData extends Request
{
    /**
     * Get data as JSON String
     *
     * @param  integer $subsession_id
     * @param  integer $simsession_number The main event is 0; the preceding event is -1, and so on.
     * @param  integer $cust_id Required if the subsession was a single-driver event. Optional for team events. If omitted for a team event then the laps driven by all the team's drivers will be included.
     * @param  integer $team_id Required if the subsession was a team event.
     * @param  bool $addChunkcontent Should chunked data be added in the field "chunkdata"
     * @return String
     */
    public function getJSON(int $subsession_id = 0, int $simsession_number = 0, int $cust_id = 0, int $team_id = 0, bool $addChunkcontent = true)
    :String
    {
        //clamp quarter to valid values
        $parameter = [
            'subsession_id' => $subsession_id,
            'simsession_number' => $simsession_number
        ];
        if($cust_id != 0) $parameter['cust_id'] = $cust_id;
        if($team_id != 0) $parameter['team_id'] = $team_id;
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/results/lap_data", $parameter)->getResponse());
        $json = json_decode(file_get_contents($json->link),1);
        if($addChunkcontent){
            $chunkinfo = $json['chunk_info'];
            $base_url = $chunkinfo['base_download_url'];
            $chunkdata = [];
            foreach($chunkinfo['chunk_file_names'] as $filename){
                $chunk = json_decode(file_get_contents($base_url.$filename), 1);
                $chunkdata = array_merge($chunkdata, $chunk);
            }
            $json['chunkdata'] = $chunkdata;
        }

        return json_encode($json);
    }

    /**
     * Get data as associative array
     *
     * @param  integer $subsession_id
     * @param  integer $simsession_number The main event is 0; the preceding event is -1, and so on.
     * @param  integer $cust_id Required if the subsession was a single-driver event. Optional for team events. If omitted for a team event then the laps driven by all the team's drivers will be included.
     * @param  integer $team_id Required if the subsession was a team event.
     * @param  bool $addChunkcontent Should chunked data be added in the field "chunkdata"
     * @return array Data in associative array
     */
    public function getArray(int $subsession_id = 0, int $simsession_number = 0, int $cust_id = 0, int $team_id = 0, bool $addChunkcontent = true)
    :array
    {
        return json_decode($this->getJSON($subsession_id, $simsession_number, $cust_id, $team_id, $addChunkcontent), true);
    }
}
