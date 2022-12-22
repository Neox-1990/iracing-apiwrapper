<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Used to access the Member Get Api endpoint
 * This endpoint gives access to member data of one or multiple members with a given id
 */
class MemberAwards extends Request
{

    /**
     * Get data as JSON
     *
     * @param integer|array $cust_ids ID(s) of the members you want to get the data from
     * @param boolean $include_license Should license information be included
     * @return String Data in JSON format
     */
    public function getJSON(int $cust_id = 0)
    :String
    {
        $parameter = [];
        if($cust_id != 0){
            $parameter['cust_id'] = $cust_id;
        }
        $curl = $this->perform("https://members-ng.iracing.com/data/member/awards", $parameter);
        $json = json_decode($curl->response);
        $this->updateRateLimit($curl);
        $curl->close();
        $json = file_get_contents($json->link);
        return $json;
    }

    /**
     * Get data as associative array
     *
     * @param integer|array $cust_ids ID(s) of the members you want to get the data from
     * @param boolean $include_license Should license information be included
     * @return array Data as associative array
     */
    public function getArray(int $cust_id = 0)
    :array
    {
        return json_decode($this->getJSON($cust_id));
    }
}
