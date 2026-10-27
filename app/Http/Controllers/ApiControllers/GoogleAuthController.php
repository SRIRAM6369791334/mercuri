<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use Google_Client;
use Illuminate\Http\Request;
use Exception;

class GoogleAuthController extends Controller
{
    private function getFCMAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/firebase-service-account.json')); // Path to your Firebase service account JSON file
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();

        // Get the access token
        $token = $client->fetchAccessTokenWithAssertion();

        if (isset($token['access_token'])) {
            return $token['access_token'];
        }

        throw new Exception("Failed to fetch access token");
    }

    private function sendPushNotification($token, $title, $body)
    {
        $accessToken = $this->getFCMAccessToken();

        // Replace with your actual project ID
        $projectId = 'rmiller-crm';

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ];

        $data = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body"  => $body
                ]
            ]
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers' => $headers,
            'json'    => $data
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody()->getContents());
        }

        throw new Exception("Error sending FCM message: " . $response->getStatusCode());
    }

    public function sendNotification()
    {
        try {
            $token = 'fmLenthXQ7ehspm9fryLXN:APA91bH-h5tN-N9K-5zULlXmFnwysmJiQnSs5cvOKMzLnPS3V8BVALHugojrKFZIKoDW-zQnACEXdHVq-6QZWb3fomPw_f4sv53pNowB4GkmozgmmvK71NU'; // Replace with actual FCM token
            $title = 'Hello';
            $body = 'This is a test message';

            $response = $this->sendPushNotification($token, $title, $body);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
