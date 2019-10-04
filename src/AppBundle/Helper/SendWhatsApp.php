<?php
/**
 * Created by PhpStorm.
 * Date: 25.06.18
 * Time: 15:20
 */

namespace AppBundle\Helper;

/**
 * Class for send WhatsApp notify
 *
 * Class SendWhatsApp
 * @package AppBundle\Helper
 */
class SendWhatsApp
{
    /**
     * Request type GET
     */
    const TYPE_GET = "get";

    /**
     * Request type POST
     */
    const TYPE_POST = "post";

    /**
     * Request type DELETE
     */
    const TYPE_DELETE = "delete";

    /**
     * @var string $client_id
     */
    private $client_id = 'graeme@bluerecruiting.co.za';

    /**
     * @var string $client_secret
     */
    private $client_secret = 'bf33039ac0ec48d8a8ec458db24acca5';

    /**
     * @var int $instance_id
     */
    private $instance_id = 16;

    /**
     * @var string $apiUrl
     */
    private $apiUrl = 'http://api.whatsmate.net/v3/whatsapp/';

    /**
     * Send Single Text
     * @param $postData
     * @return mixed
     */
    public function sendSingleText($postData){
        return $this->call('single/text/message', $postData);
    }

    /**
     * Request to url
     *
     * @param $url
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function call($url, $data, $type = self::TYPE_POST)
    {
        $headers = array(
            'Content-Type: application/json',
            'X-WM-CLIENT-ID: '.$this->client_id,
            'X-WM-CLIENT-SECRET: '.$this->client_secret
        );

        if ($type == self::TYPE_GET) {
            $url .= '?'.http_build_query($data);
        }

        $process = curl_init($this->apiUrl.$url.'/'.$this->instance_id);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 20);

        if($type == self::TYPE_POST || $type == self::TYPE_DELETE) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($type == self::TYPE_DELETE) {
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return, true);
    }

}