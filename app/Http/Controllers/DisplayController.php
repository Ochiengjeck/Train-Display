<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRegistration;
use Illuminate\Support\Facades\Http;

class DisplayController extends Controller
{
    public function showDisplay()
    {
        $device = DeviceRegistration::first();
        
        if (!$device || !$device->api_token) {
            return redirect()->route('register.form');
        }

        // Fetch initial data
        $data = $this->fetchDisplayData($device);
        
        return view('display', $data);
    }

    public function refreshDisplay()
    {
        $device = DeviceRegistration::first();
        
        if (!$device || !$device->api_token) {
            return response()->json(['error' => 'Device not registered'], 401);
        }

        $data = $this->fetchDisplayData($device);
        
        return response()->json($data);
    }

    private function fetchDisplayData($device)
    {
        $notices = [];
        $trips = [];    
        $error = null;

        try {
            // Get trip notices
            $noticesResponse = Http::withToken($device->api_token)
                ->get('https://train.centricltd.co.ke/api/trips/notices');
                
            if ($noticesResponse->successful()) {
                $noticesData = $noticesResponse->json();
                $notices = $noticesData['data'] ?? [];
            }

            // Get upcoming trips
            $tripsResponse = Http::withToken($device->api_token)
                ->get('https://train.centricltd.co.ke/api/trips', [
                    'origin' => $device->station_id ?? '1',
                ]);
                
            if ($tripsResponse->successful()) {
                $tripsData = $tripsResponse->json();
                $trips = $tripsData['data']['data'] ?? [];
            }
        } catch (\Exception $e) {
            $error = 'Failed to fetch data from API: ' . $e->getMessage();
        }

        return [
            'notices' => $notices,
            'trips' => $trips,
            'error' => $error,
            'stationName' => $device->station_name,
        ];
    }
}