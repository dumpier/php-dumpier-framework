<?php
namespace Presto\Oauth;

use Presto\Core\Traits\Singletonable;

class Google
{
    use Singletonable;


    public function get_auth_url()
    {
        $google = config("auth", "google.api");
        $client = config("auth", "google.client");
        $callback = config("auth", "google.client.callback");

        $querys = [];
        $querys['client_id'] = $client["id"];
        $querys['redirect_uri'] = http()->url($callback);
        $querys['scope'] = $google["scope"];
        $querys['response_type'] = $client["response_type"];

        return $google["auth"] . http_build_query($querys);
    }


    public function token(string $code)
    {
        $client = config("auth", "google.client");
        $google_api = config("auth", "google.api.token");

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


    private $userinfos = [];

    /**
     * GoogleのUserInfoの取得
     * @param string $access_token
     * @return string
     */
    public function userInfo(string $access_token)
    {
        if($this->userinfos[$access_token])
        {
            return $this->userinfos[$access_token];
        }

        $google_api = config("auth", "google.api.userinfo");

        if (empty($access_token))
        {
            return null;
        }

        $this->userinfos[$access_token] = json_decode(file_get_contents("{$google_api}{$access_token}"));

        if (empty($this->userinfos[$access_token]))
        {
            return null;
        }

        return $this->userinfos[$access_token];
    }


    public function userId(string $access_token)
    {
        $userInfo = $this->userInfo($access_token);

        if($userInfo)
        {
            return $userInfo->id;
        }

        return null;
    }
}