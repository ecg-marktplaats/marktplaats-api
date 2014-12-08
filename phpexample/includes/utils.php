<?php
/**
 * Created by IntelliJ IDEA.
 * User: msturm
 * Date: 08-12-14
 * Time: 15:45
 */

function retrieveAttributes($apiUrl, $l1id, $l2id, $accessToken) {
    $url = $apiUrl . "/v1/categories/$l1id/$l2id/attributes";
    return apiCall($url, '', $accessToken);
}

function apiCall($url, $data, $accessToken, $method='GET') {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
            "Authorization: Bearer $accessToken")
    );

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    return json_decode($curlResult);
}