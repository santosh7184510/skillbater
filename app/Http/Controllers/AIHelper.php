<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;



class AIHelper
{
    public static function aiResponse($question)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('QROKE_API_KEY'),
            ])->post('https://api.qroke.ai/v1/chat/completions', [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    ["role"=>"user","content"=>$question]
                ],
                "temperature" => 0.7,
                "max_tokens" => 500
            ]);

            $data = $response->json();

            return $data['choices'][0]['message']['content'] ?? "⚠️ No response from AI";

        } catch (\Exception $e) {
            return "⚠️ AI server not reachable: " . $e->getMessage();
        }
    }
}
