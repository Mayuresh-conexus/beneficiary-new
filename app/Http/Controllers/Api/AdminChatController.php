<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminAiService;

class AdminChatController extends Controller
{
    protected $aiService;

    public function __construct(AdminAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->aiService->chat($request->message, $request->user());

        if (isset($response['error'])) {
            return response()->json($response, 500);
        }

        return response()->json($response);
    }
}
