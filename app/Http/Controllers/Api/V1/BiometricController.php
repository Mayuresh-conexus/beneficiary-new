<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiometricController extends Controller
{
    /**
     * Parse RD Service XML to extract Template and Device Info
     */
    public function parsePidXml(Request $request)
    {
        $request->validate([
            'pid_xml' => 'required|string',
        ]);

        $xmlString = $request->pid_xml;

        try {
            // Parse XML
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid XML format',
                    'error_code' => 'XML_PARSE_ERROR'
                ], 400);
            }

            // Extract Response Code
            $resp = $xml->Resp;
            $errCode = (string)$resp['errCode'];
            $errInfo = (string)$resp['errInfo'];

            if ($errCode !== '0') {
                return response()->json([
                    'success' => false,
                    'message' => 'Device Logic Error: ' . $errInfo,
                    'error_code' => $errCode
                ], 400);
            }

            // Extract Template Data
            $data = (string)$xml->Data;

            // Extract Device Info
            $deviceInfo = $xml->DeviceInfo;
            $dc = (string)$deviceInfo['dc'];
            $dpId = (string)$deviceInfo['dpId'];
            $rdsId = (string)$deviceInfo['rdsId'];
            $mi = (string)$deviceInfo['mi'];
            $mc = (string)$deviceInfo['mc'];

            // Extract Quality
            $qScore = (string)$resp['qScore'];

            return response()->json([
                'success' => true,
                'data' => [
                    'template' => $data,
                    'quality_score' => $qScore,
                    'device_info' => [
                        'dc' => $dc,
                        'dpId' => $dpId,
                        'rdsId' => $rdsId,
                        'mi' => $mi,
                        'mc' => $mc,
                        'full_string' => "$dc | $dpId | $mi"
                    ]
                ]
            ]);

        }
        catch (\Exception $e) {
            Log::error('Biometric XML Parse Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error parsing Biometric XML: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Verify Biometric Template using Python Backend
     */
    public function verifyBiometric(Request $request)
    {
        $request->validate([
            'captured_template' => 'required|string',
            'stored_template' => 'required|string',
        ]);

        $t1 = $request->captured_template;
        $t2 = $request->stored_template;

        // Path to Python Script
        $scriptPath = storage_path('app/biometrics/match.py');

        if (!file_exists($scriptPath)) {
            return response()->json(['success' => false, 'message' => 'Match Script not found.'], 500);
        }

        try {
            // Execution
            // Note: Command line arguments might be too long for large templates. 
            // Better to write to temp files if templates are huge, but for ISO (approx 500 chars), arguments are fine.
            // standard ISO template is ~400-800 bytes base64. Limit is usually 8KB or 32KB. Should be safe.

            $process = new \Symfony\Component\Process\Process([
                'python',
                $scriptPath,
                $t1,
                $t2
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Symfony\Component\Process\Exception\ProcessFailedException($process);
            }

            $output = $process->getOutput();
            $score = (int)trim($output);

            return response()->json([
                'success' => true,
                'score' => $score,
                'match' => ($score >= 80) // Threshold can be adjusted
            ]);

        }
        catch (\Exception $e) {
            Log::error('Biometric Match Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Match Algorithm Failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
