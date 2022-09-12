<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Get lapchart of every participant in session, like the laps page ingame. 
 * The data comes in chunks and will be added to the return by default
 */
class ResultsLapChartData extends Request
{
    /**
     * Get data as JSON String
     * Usually returns only the links to the data chunks on aws
     * With the addChunkContent, thosse data chunks will be fetched as well and 
     * combined (default behavior)
     *
     * @param  integer $subsession_id
     * @param  integer $simsession_number The main event is 0; the preceding event is -1, and so on.
     * @param  bool $addChunkcontent Should chunked data be added in the field "chunkdata"
     * @return String
     */
    public function getJSON(int $subsession_id, int $simsession_number, bool $addChunkcontent = true)
    :String
    {
        //clamp quarter to valid values
        $parameter = [
            'subsession_id' => $subsession_id,
            'simsession_number' => $simsession_number
        ];
        $json = json_decode($this->perform("https://members-ng.iracing.com/data/results/lap_chart_data", $parameter)->getResponse());
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
     * Usually returns only the links to the data chunks on aws
     * With the addChunkContent, thosse data chunks will be fetched as well and 
     * combined (default behavior)
     *
     * @param  integer $subsession_id
     * @param  integer $simsession_number The main event is 0; the preceding event is -1, and so on.
     * @param  bool $addChunkcontent Should chunked data be added in the field "chunkdata"
     * @return array Data in associative array
     */
    public function getArray(int $subsession_id, int $simsession_number, bool $addChunkcontent = true)
    :array
    {
        return json_decode($this->getJSON($subsession_id, $simsession_number, $addChunkcontent), true);
    }
}
