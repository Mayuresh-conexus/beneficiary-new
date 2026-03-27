# Admin AI Integration Master Guide

This guide outlines the complete setup for adding an intelligent Admin AI Assistant to a Laravel application. This assistant can query your database via function calling and provide natural language responses.

## 1. System Requirements

*   **Laravel 10+** (or compatible PHP framework)
*   **Google Gemini API Key** (Get it from [Google AI Studio](https://aistudio.google.com/))
*   **PHP 8.1+**

## 2. Environment Configuration (`.env`)

Add your API key to the `.env` file:
```env
GEMINI_API_KEY=your_actual_api_key_here
```

## 3. Service Configurations (`config/services.php`)

Register the configuration so Laravel can access it:
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
],
```

## 4. The Core AI Service (`app/Services/AdminAiService.php`)

This is the brain of the operation. It handles the API communication, tool definitions, and function execution.

**Key Features:**
*   **Function Calling:** Defines tools describing your PHP functions to the AI.
*   **Parts Handling:** Preserves the full "Chain of Thought" context required by newer Gemini models.
*   **Robust Error Handling:** Logs errors and handles API timeouts.

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// Import your Models here
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\User;
use App\Models\Transaction;

class AdminAiService
{
    protected $apiKey;
    // Use 'gemini-2.5-flash' for the latest stable speed and performance
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat(string $message, User $user)
    {
        // 1. Define Tools
        $tools = $this->getTools();

        // 2. Build Payload
        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $message]]]
            ],
            'tools' => [['function_declarations' => $tools]],
            'tool_config' => ['function_calling_config' => ['mode' => 'AUTO']],
            'system_instruction' => [
                'parts' => [['text' => 'You are a helpful Admin Assistant. Use the provided tools to fetch real data. Current Date: ' . now()->toDateString()]]
            ]
        ];

        try {
            // 3. Call Gemini API
            $response = Http::timeout(60) // Increased timeout for complex queries
                ->withoutVerifying() // Optional: Only for local dev if SSL fails
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error', $response->json());
                return ['error' => 'API Error: ' . $response->status()];
            }

            $responseData = $response->json();
            $candidate = $responseData['candidates'][0] ?? null;

            // 4. Handle Function Calls (Preserving Context)
            if (isset($candidate['content']['parts'])) {
                $parts = $candidate['content']['parts'];
                foreach ($parts as $part) {
                    if (isset($part['functionCall'])) {
                        // Pass ALL parts to preserve "thought_signature"
                        return $this->handleFunctionCall($parts, $message); 
                    }
                }
            }

            // 5. Return Text Response
            return ['message' => $candidate['content']['parts'][0]['text'] ?? 'No response text.'];

        } catch (\Exception $e) {
            Log::error('AI Exception: ' . $e->getMessage());
            return ['error' => 'Internal Server Error'];
        }
    }

    protected function handleFunctionCall(array $modelParts, string $originalMessage)
    {
        // Extract function call details
        $functionCall = null;
        foreach ($modelParts as $part) {
            if (isset($part['functionCall'])) {
                $functionCall = $part['functionCall'];
                break;
            }
        }

        $functionName = $functionCall['name'];
        $args = $functionCall['args'] ?? [];
        $result = null;

        // Execute PHP Logic
        switch ($functionName) {
            case 'get_beneficiary_details':
                $result = $this->getBeneficiaryDetails($args['search_term'] ?? '');
                break;
            // Add more cases here...
            default:
                $result = "Function $functionName not found.";
        }

        // Send Result Back to AI
        return $this->generateFinalResponse($originalMessage, $modelParts, $functionName, $result);
    }

    protected function generateFinalResponse(string $originalMessage, array $modelParts, string $functionName, $functionResult)
    {
        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $originalMessage]]],
                ['role' => 'model', 'parts' => $modelParts], // CRITICAL: Send back original thoughts/calls
                ['role' => 'function', 'parts' => [['functionResponse' => [
                    'name' => $functionName,
                    'response' => ['content' => $functionResult]
                ]]]]
            ]
        ];

        // Final API Call
        $response = Http::timeout(60)
            ->withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);
            
        return [
            'message' => $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Here is the data.',
            'data_context' => $functionResult
        ];
    }

    // --- Tool Definitions ---
    protected function getTools()
    {
        return [
            [
                'name' => 'get_beneficiary_details',
                'description' => 'Fetch beneficiary info by name/ID.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search_term' => ['type' => 'STRING', 'description' => 'Name or ID']
                    ],
                    'required' => ['search_term']
                ]
            ],
            // Add more tools definitions...
        ];
    }

    // --- Helper Methods ---
    protected function getBeneficiaryDetails($term) {
        // Your database logic here
        return Beneficiary::where('name', 'like', "%$term%")->first() ?? 'Not found';
    }
}
```

## 5. Web Route (`routes/web.php`)

Use the `web` middleware group to leverage existing session authentication.

```php
use App\Http\Controllers\Api\AdminChatController;

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/chat', [AdminChatController::class, 'sendMessage'])->name('admin.chat');
});
```

## 6. Controller (`AdminChatController.php`)

```php
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
        $request->validate(['message' => 'required|string']);
        $response = $this->aiService->chat($request->message, $request->user());
        return response()->json($response);
    }
}
```

## 7. Frontend Integration (JS)

```javascript
async function sendChatRequest(message) {
    const response = await fetch('/admin/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ message: message })
    });

    const data = await response.json();
    console.log("AI Response:", data.message);
    if(data.data_context) console.log("Raw Data:", data.data_context);
}
```

## Optimization Tips
*   **Model Selection:** `gemini-1.5-flash` is 10x faster than Pro models. Only use Pro/Thinking models if complex reasoning is needed.
*   **Data Size:** Only return necessary fields from DB queries (e.g., avoid returning large BLOBs or unused columns).
*   **Timeouts:** Ensure your PHP and Web Server timeouts match the API timeout (60s+).
