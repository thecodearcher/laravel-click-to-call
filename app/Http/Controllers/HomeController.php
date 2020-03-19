<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function placeCall(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|numeric',
        ]);

        $to_number = $request->input('phoneNumber');

        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");

        $client = new Client($account_sid, $auth_token);

        $pin = implode('. ', str_split(rand(10000, 99999)));

        $client->account->calls->create(
            $to_number,
            $twilio_number,
            [
                "twiml" => $this->callMessage($pin),
            ]
        );
        return view('welcome', ['message' => 'You will receive a call shortly!']);
    }

    public function callMessage($pin)
    {
        $response = new VoiceResponse();
        $response->say("Your Laravel test pin is: $pin.");
        $response->say("GoodBye!");
        $response->hangup();
        return $response;
    }
}
