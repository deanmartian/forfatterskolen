<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        return view('frontend.chat.index');
    }

    public function sendMessage(Request $request): JsonResponse
    {

        // Get the user's message from the request
        $message = $request->input('message');

        // Make an API request to the ChatGPT model
        $client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('services.gpt.api_key'),
        ];

        $data = [
            // 'model' => 'text-davinci-002',
            // 'prompt' => $message,
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant for https://www.forfatterskolen.no website.'],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0.5,
            'max_tokens' => 50,
        ];

        $response = $client->post('chat/completions', [
            'headers' => $headers,
            'json' => $data,
        ]);

        $responseData = json_decode($response->getBody(), true);

        // $answer = $responseData['choices'][0]['text'];
        $answer = $responseData['choices'];

        // Return the ChatGPT response to the user
        return response()->json([
            'message' => $answer,
        ]);
    }
}
