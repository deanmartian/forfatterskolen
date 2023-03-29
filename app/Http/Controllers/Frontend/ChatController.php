<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatController extends Controller
{
    
    public function index()
    {
        return view('frontend.chat.index');
    }

    public function sendMessage(Request $request)
    {
        // Get the user's message from the request
        $message = $request->input('message');

        // Make an API request to the ChatGPT model
        $client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.gpt.api_key'),
        ];

        $data = [
            'prompt' => $message,
            'temperature' => 0.5,
            'max_tokens' => 50,
        ];

        $response = $client->post('engines/davinci-codex/completions', [
            'headers' => $headers,
            'json' => $data,
        ]);

        $responseData = json_decode($response->getBody(), true);

        $answer = $responseData['choices'][0]['text'];

        // Return the ChatGPT response to the user
        return response()->json([
            'message' => $answer,
        ]);
    }

}
