<?php


namespace App;


use App\Events\WsMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Utils
{

    public static function timeBetweenDatesMillis($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $seconds = $end->diffInSeconds($start);
        return $seconds;
    }

    public static function percentage($total, $current)
    {
        if ($total == 0) {
            return 0;
        }

        $percent = intval((100 * $current) / $total);
        return max(min($percent, 100), 0);;
    }

    public static function sendRequestToAgent($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://agent.redops.me/jobs");
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));


        $response = curl_exec($ch);
        $info = curl_errno($ch) > 0 ? array("curl_error_" . curl_errno($ch) => curl_error($ch)) : curl_getinfo($ch);
        print_r($info);
        curl_close($ch);
        return $info;
    }

    public static function sendNotificationUser($userRid, $title, $text, $type) {
        $notificationOptions = array(
            'title' => $title,
            'text' => $text,
            'type' => $type,
        );
        event(new WsMessage($userRid, 'notification', json_encode($notificationOptions)));
    }

}