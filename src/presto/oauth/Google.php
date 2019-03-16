<?php
namespace Presto\Oauth;

use Presto\Core\Traits\Singletonable;

class Google
{
    use Singletonable;


    public function get_auth_url()
    {
        $google = config()->get("auth", "google.api");
        $client = config()->get("auth", "google.client");

        $querys = [];
        $querys['client_id'] = $client["id"];
        $querys['redirect_uri'] = $client["callback"];
        $querys['scope'] = $google["scope"];
        $querys['response_type'] = $client["code"];

        return $google["auth"] . http_build_query($querys);
    }


    public function token(string $code)
    {
        $client = config()->get("auth", "google.client");
        $google_api = config()->get("auth", "google.api.token");

        $params = [];
        $params["code"] = $client["code"];;
        $params["client_id"] = $client["id"];
        $params["client_secret"] = $client["secret"];
        $params["redirect_uri"] = $client["callback"];
        $params["grant_type"] = "authorization_code";

        $headers = [];
        $headers[] = "Content-Type: application/x-www-form-urlencoded";

        $options = [];
        $options["http"]["method"] = "POST";
        $options["http"]["content"] = http_build_query($params);
        $options["http"]["header"] = implode("\r\n", $headers);

        $response = json_decode(file_get_contents($google_api, false, stream_context_create($options)));

        if(!$response || isset($response->error))
        {
            return null;
        }

        return $response->access_token;
    }


    public function userId(string $access_token)
    {
        $google_api = config()->get("auth", "google.api.userinfo");

        if (empty($access_token))
        {
            return null;
        }

        $userInfo = json_decode(file_get_contents("{$google_api}{$access_token}"));

        if (empty($userInfo))
        {
            return null;
        }

        return $userInfo->id;
    }
}