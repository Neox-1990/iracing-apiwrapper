<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Used to access the Stats Member Career Api endpoint
 * Returns basic career information for each category (road, oval, ...)
 */
class StatsMemberCareer extends Request
{
    /**
     * Returns JSON Data
     *
     * @param  integer $cust_id Id of driver. If ommited, authenticated driver will be used
     * @return String
     */
    public function getJSON(int $cust_id = 0)
    :String
    {
        $parameter = [];
        if($cust_id != 0){
            $parameter['cust_id'] = $cust_id;
        }
        $curl = $this->perform("https://members-ng.iracing.com/data/stats/member_career", $parameter);
        $json = json_decode($curl->response);
        $this->updateRateLimit($curl);
        $curl->close();
        $json = file_get_contents($json->link);
        return $json;
    }

    /**
     * Get data as associative array
     *
     * @param  integer $cust_id Id of driver. If ommited, authenticated driver will be used
     * @return array
     */
    public function getArray(int $cust_id = 0)
    :array
    {
        return json_decode($this->getJSON(), true);
    }
}
