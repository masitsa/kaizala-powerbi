<?php

class Kaizala_model extends CI_Model
{
    public $application_id;
    public $application_secret;
    public $refresh_token;
    public $access_token_url;

    public function __construct()
    {
        $this->application_id = "7138daff-6c9f-4c69-8917-201df3d0b0e2";
        $this->application_secret = "JP44T86MHZ";
        $this->refresh_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1cm46bWljcm9zb2Z0OmNyZWRlbnRpYWxzIjoie1wicGhvbmVOdW1iZXJcIjpcIisyNTQ3MjYxNDkzNTFcIixcImNJZFwiOlwiXCIsXCJ0ZXN0U2VuZGVyXCI6XCJmYWxzZVwiLFwiYXBwTmFtZVwiOlwiY29tLm1pY3Jvc29mdC5tb2JpbGUua2FpemFsYWFwaVwiLFwiYXBwbGljYXRpb25JZFwiOlwiNzEzOGRhZmYtNmM5Zi00YzY5LTg5MTctMjAxZGYzZDBiMGUyXCIsXCJwZXJtaXNzaW9uc1wiOlwiOC40XCIsXCJhcHBsaWNhdGlvblR5cGVcIjotMSxcImRhdGFcIjpcIntcXFwiVGVuYW50SWRcXFwiOlxcXCJhZmRmMjk3Zi1iNzEzLTQ1Y2UtYTA3Mi1lY2EyYTBlYzA1MzNcXFwiLFxcXCJPMzY1VXNlcklkXFxcIjpcXFwiNTkxNzA5MjQtNDMyYS00NTkyLWFkNGMtNDIxMGExNzk2NzkwXFxcIixcXFwiSXNUZW5hbnRBZG1pblxcXCI6XFxcIlRydWVcXFwiLFxcXCJPMzY1VXNlckVtYWlsSWRcXFwiOlxcXCJhbHZhcm8ubWFzaXRzYUBuYW55dWtpYXBwZmFjdG9yeS5vbm1pY3Jvc29mdC5jb21cXFwiLFxcXCJPMzY1VXNlck5hbWVcXFwiOlxcXCJBbHZhcm8gTWFzaXRzYVxcXCIsXFxcIkFwcE5hbWVcXFwiOlxcXCJQQkkgUmVwb3J0XFxcIn1cIn0iLCJ1aWQiOiJNb2JpbGVBcHBzU2VydmljZTo4NmZlYjUyYy0xNGQ1LTRhN2QtOThkYS1iYTJhYjQ0MGYwOGYiLCJ2ZXIiOiIyIiwibmJmIjoxNTUyOTc5MzEyLCJleHAiOjE1ODQ1MTUzMTIsImlhdCI6MTU1Mjk3OTMxMiwiaXNzIjoidXJuOm1pY3Jvc29mdDp3aW5kb3dzLWF6dXJlOnp1bW8iLCJhdWQiOiJ1cm46bWljcm9zb2Z0OndpbmRvd3MtYXp1cmU6enVtbyJ9.aWYXUeNqGIpbiWsv84t6Wgm-1Mb2c421Ra_lspcBZvI";

        $this->access_token_url = "https://kms.kaiza.la/v1/accessToken";
    }

    private function get_access_token()
    {
        // Performing the HTTP request
        $ch = curl_init($this->access_token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'applicationId: ' . $this->application_id,
                'applicationSecret: ' . $this->application_secret,
                'refreshToken: ' . $this->refresh_token,
                'Content-Type: application/json',
            )
        );
        $response_body = curl_exec($ch);
        curl_close($ch);

        $response_json = json_decode($response_body);
        return $response_json->accessToken;
    }

    public function fetch_groups()
    {
        $access_token = $this->get_access_token();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kms.kaiza.la/v1/groups?fetchAllGroups=true&showDetails=true",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accessToken: " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            // var_dump(json_decode($response));die();
            $result = json_decode($response);
            if (array_key_exists('groups', $result)) {
                $groups = $result->groups;
                return array(true, $groups);
            } else {
                return array(false, $result);
            }

        }
    }

    public function get_group_users($group_id, $control)
    {
        $curl = curl_init();

        $access_token = $this->get_access_token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kms.kaiza.la/v1/groups/" . $group_id . "/" . $control,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accessToken: " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array('error' => $err);
        } else {
            $response_obj = json_decode($response);

            if ($control == 'members') {
                if (array_key_exists('members', $response_obj)) {
                    if (count($response_obj->members) > 0) {
                        return $response_obj->members;
                    } else {
                        return false;
                    }
                }

            } else if ($control == 'subscribers') {
                if (array_key_exists('subscribers', $response_obj)) {
                    if (count($response_obj->subscribers) > 0) {
                        return $response_obj->subscribers;
                    } else {
                        return false;
                    }
                }

            }

        }
    }

    public function create_event_webhook($group_unique_id, $base_url)
    {
        $send_data = array(
            "objectId" => $group_unique_id,
            "objectType" => "Group",
            "eventTypes" => array(
                "ActionCreated",
                "ActionResponse",
                "SurveyCreated",
                "JobCreated",
                "SurveyResponse",
                "JobResponse",
                "Announcement",
                "MemberAdded",
                "MemberRemoved",
            ),
            "callBackUrl" => $base_url . "actions/get-actions",
            "callBackToken" => "tokenToBeVerifiedByCallback",
            "callBackContext" => $base_url . "actions/get-actions",
        );

        $curl = curl_init();

        $access_token = $this->get_access_token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kms.kaiza.la/v1/webhook",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($send_data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "accessToken: " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $error = "cURL Error #:" . $err;
            return array(false, $error);
        } else {
            $obj = json_decode($response);
            if (array_key_exists('webhookId', $obj)) {
                $webhook_id = $obj->webhookId;
                return array(true, $webhook_id);
            }
            return array(false, $obj);
        }
    }

    public function delete_event_webhook($group_unique_id)
    {
        $curl = curl_init();

        $access_token = $this->get_access_token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kms.kaiza.la/v1/webhook/" . $group_unique_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accessToken: " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $error = "cURL Error #:" . $err;
            return array(false, $error);
        } else {
            $obj = json_decode($response);
            if (array_key_exists('message', $obj)) {
                return array(
                    false,
                    $obj,
                );
            } else {
                return array(
                    true,
                    $obj,
                );
            }
        }
    }

    public function all_webhooks($group_id)
    {
        $curl = curl_init();

        $access_token = $this->get_access_token();

        // return array('access' => $access_token);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kms.kaiza.la/v1/webhook?objectId=" . $group_id . "&objectType=Group",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accessToken: " . $access_token,
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array(
                false,
                "cURL Error #:" . $err,
            );
        } else {
            $obj = json_decode($response);

            return array(
                true,
                $obj,
            );
        }
    }
}
