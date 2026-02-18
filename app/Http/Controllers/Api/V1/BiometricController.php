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
}
