<?php
namespace Neox1990\IracingApiwrapper\Requests;

use \Neox1990\IracingApiwrapper\Request;

/**
 * Used to access the Member Chart_Data Api endpoint
 */
class MemberChartData extends Request
{

    /**
     * Gets data as JSON String
     *
     * @param  integer $cust_id ID of driver of interest
     * @param  integer $category_id Racing category: 1 - Oval, 2 - Road, 3 - Dirt oval, 4 - Dirt road
     * @param  integer $chart_type Type of chart: 1 - iRating; 2 - TT Rating; 3 - License/SR
     * @return String JSON Data
     */
    public function getJSON(int $cust_id = 0, int $category_id = 1, int $chart_type = 1)
    :String
    {
        $category_id = in_array($category_id, [1,2,3,4]) ? $category_id : 1;
        $chart_type = in_array($chart_type, [1,2,3]) ? $chart_type : 1;
        $parameter = [
            'cust_id' => $cust_id,
            'category_id' => $category_id,
            'chart_type' => $chart_type
        ];
        $curl = $this->perform("https://members-ng.iracing.com/data/member/chart_data", $parameter);
        $json = json_decode($curl->response);
        $this->updateRateLimit($curl);
        $curl->close();
        $json = file_get_contents($json->link);
        return $json;
    }


    public function getArray(int $cust_id = 0, int $category_id = 1, int $chart_type = 1)
    :array
    {
        return json_decode($this->getJSON($cust_id, $category_id, $chart_type));
    }
}
