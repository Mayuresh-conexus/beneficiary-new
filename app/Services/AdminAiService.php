<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\User;
use App\Models\Transaction;

class AdminAiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat(string $message, User $user)
    {
        if (!$this->apiKey) {
            return [
                'error' => 'Gemini API key is missing. Please configure it in .env file.'
            ];
        }

        $cacheKey = "admin_ai_history_{$user->id}";
        $history = Cache::get($cacheKey, []);

        $history[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $tools = $this->getTools();

        $payload = [
            'contents' => $history,
            'tools' => [
                ['function_declarations' => $tools]
            ],
            'tool_config' => [
                'function_calling_config' => ['mode' => 'AUTO']
            ],
            'system_instruction' => $this->getSystemInstruction()
        ];

        try {
            $response = Http::timeout(60)
                ->retry(3, 2000)
                ->withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['error' => 'Failed to communicate with AI service. Status: ' . $response->status()];
            }

            $responseData = $response->json();

            // Handle Function Calling
            $candidate = $responseData['candidates'][0] ?? null;
            if ($candidate && isset($candidate['content']['parts'])) {
                $parts = $candidate['content']['parts'];
                foreach ($parts as $part) {
                    if (isset($part['functionCall'])) {
                        return $this->handleFunctionCall($parts, $message, $history, $cacheKey);
                    }
                }
            }

            // Standard Text Response
            $responseText = $candidate['content']['parts'][0]['text'] ?? null;

            if ($responseText) {
                // Add the model's text response to history
                $history[] = [
                    'role' => 'model',
                    'parts' => [['text' => $responseText]]
                ];
                // Limit history to last 20 interactions
                if (count($history) > 20) {
                    $history = array_slice($history, -20);
                }
                Cache::put($cacheKey, $history, now()->addHours(4));
            }

            return [
                'message' => $responseText ?? 'I could not process that request.'
            ];

        }
        catch (\Exception $e) {
            Log::error('AI Service Exception: ' . $e->getMessage());
            return ['error' => 'An internal error occurred while processing your request.'];
        }
    }

    protected function handleFunctionCall(array $modelParts, string $originalMessage, array $history, string $cacheKey)
    {
        // Find the function call part
        $functionCall = null;
        foreach ($modelParts as $part) {
            if (isset($part['functionCall'])) {
                $functionCall = $part['functionCall'];
                break;
            }
        }

        if (!$functionCall) {
            return ['error' => 'Invalid function call structure.'];
        }

        $functionName = $functionCall['name'];
        $args = $functionCall['args'] ?? [];

        Log::info("AI Function Call: $functionName", $args);
        Log::info("Full Model Parts:", $modelParts);

        $plausible_data = null;

        switch ($functionName) {
            case 'get_beneficiary_details':
                $plausible_data = $this->getBeneficiaryDetails($args['search_term'] ?? '');
                break;
            case 'get_project_summary':
                $plausible_data = $this->getProjectSummary($args['project_name'] ?? null);
                break;
            case 'get_volunteer_activity':
                $plausible_data = $this->getVolunteerActivity($args['volunteer_name'] ?? '');
                break;
            case 'get_transaction_stats':
                $plausible_data = $this->getTransactionStats();
                break;
            default:
                $plausible_data = "Function $functionName not found.";
        }

        // Send function result back to Gemini to generate final natural language response
        return $this->generateFinalResponse($originalMessage, $modelParts, $functionName, $plausible_data, $history, $cacheKey);
    }

    protected function generateFinalResponse(string $originalMessage, array $modelParts, string $functionName, $functionResult, array $history, string $cacheKey)
    {
        // Add the model's tool call directly into history
        $history[] = [
            'role' => 'model',
            'parts' => $modelParts
        ];

        // Add the function execution result
        $history[] = [
            'role' => 'function',
            'parts' => [['functionResponse' => [
                        'name' => $functionName,
                        'response' => ['content' => $functionResult]
                    ]]]
        ];

        $payload = [
            'contents' => $history,
            'system_instruction' => $this->getSystemInstruction()
        ];

        $response = Http::timeout(60)
            ->retry(3, 2000)
            ->withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

        if ($response->failed()) {
            Log::error('Gemini Final Response Error', ['status' => $response->status(), 'body' => $response->body()]);
            return ['error' => 'Failed to generate final response. Status: ' . $response->status()];
        }

        $responseData = $response->json();
        $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($responseText) {
            // Append final summarized text
            $history[] = [
                'role' => 'model',
                'parts' => [['text' => $responseText]]
            ];
            // Limit history
            if (count($history) > 20) {
                $history = array_slice($history, -20);
            }
            Cache::put($cacheKey, $history, now()->addHours(4));
        }

        return [
            'message' => $responseText ?? 'Here is the data you requested (AI returned no text).',
            'data_context' => $functionResult // Optional: send raw data to frontend if needed
        ];
    }

    protected function getSystemInstruction()
    {
        return [
            'parts' => [['text' => 'You are a helpful Admin Assistant for a Beneficiary Management System. 
            Your role is to assist the admin by querying the database for information about Beneficiaries, Projects, Volunteers (Users), and Transactions. 
            ALWAYS use the provided tools to fetch real data. Do not make up IDs or data. 
            If the user asks about something outside the scope of this system (e.g., "tell me a joke", "weather", "general knowledge"), politely refuse and remind them you are here to help with the admin panel data.
            Response should be concise and formatted in Markdown.
            Current Date: ' . now()->toDateString()]]
        ];
    }

    // --- Tools Definitions ---

    protected function getTools()
    {
        return [
            [
                'name' => 'get_beneficiary_details',
                'description' => 'Search for a beneficiary by name, email, or government ID and get their full details including status and assigned project.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search_term' => ['type' => 'STRING', 'description' => 'The name, email, or ID of the beneficiary.']
                    ],
                    'required' => ['search_term']
                ]
            ],
            [
                'name' => 'get_project_summary',
                'description' => 'Get details about a specific project or list all active projects if no name is provided.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'project_name' => ['type' => 'STRING', 'description' => 'The name of the project to search for. Optional.']
                    ]
                ]
            ],
            [
                'name' => 'get_volunteer_activity',
                'description' => 'Get information about a volunteer (user) and their recent activities like added beneficiaries or deliveries.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'volunteer_name' => ['type' => 'STRING', 'description' => 'The name or email of the volunteer.']
                    ],
                    'required' => ['volunteer_name']
                ]
            ],
            [
                'name' => 'get_transaction_stats',
                'description' => 'Get statistics about recent transactions, total amount distributed, and pending deliveries.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => (object)[]
                ]
            ]
        ];
    }

    // --- Data Retrieval Methods ---

    protected function getBeneficiaryDetails($searchTerm)
    {
        $beneficiary = Beneficiary::where('first_name', 'like', "%$searchTerm%")
            ->orWhere('last_name', 'like', "%$searchTerm%")
            ->orWhere('government_id', 'like', "%$searchTerm%")
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
            ->with(['project', 'packages'])
            ->first();

        Log::info('Beneficiary Data Retrieved:', ['data' => $beneficiary]);

        if (!$beneficiary) {
            return "No beneficiary found matching '$searchTerm'.";
        }

        return $beneficiary->toArray();
    }

    protected function getProjectSummary($projectName = null)
    {
        if ($projectName) {
            $project = Project::where('name', 'like', "%$projectName%")
                ->withCount(['beneficiaries', 'assignedUsers'])
                ->first();

            if (!$project)
                return "Project '$projectName' not found.";

            return $project->toArray();
        }

        return Project::select('name', 'status', 'start_date', 'end_date')->limit(5)->get()->toArray();
    }

    protected function getVolunteerActivity($volunteerName)
    {
        $user = User::where('name', 'like', "%$volunteerName%")
            ->orWhere('email', 'like', "%$volunteerName%")
            ->first();

        if (!$user)
            return "Volunteer '$volunteerName' not found.";

        $submittedCount = Beneficiary::where('submitted_by', $user->id)->count();
        $recentDeliveries = Transaction::where('delivered_by', $user->id)
            ->with(['beneficiary:id,first_name,last_name'])
            ->latest()
            ->limit(3)
            ->get();

        return [
            'volunteer' => $user->name,
            'role' => $user->role,
            'total_beneficiaries_submitted' => $submittedCount,
            'recent_deliveries' => $recentDeliveries->toArray()
        ];
    }

    protected function getTransactionStats()
    {
        return [
            'total_transactions' => Transaction::count(),
            'total_amount' => Transaction::sum('amount'),
            'completed' => Transaction::where('status', 'completed')->count(),
            'pending' => Transaction::where('status', 'pending')->count(),
        ];
    }
}
