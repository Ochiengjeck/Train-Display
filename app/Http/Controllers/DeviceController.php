<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    public function showRegistrationForm()
    {
        // Check if device is already registered
        $device = DeviceRegistration::first();
        if ($device && $device->api_token) {
            return redirect()->route('display');
        }
        
        return view('register');
    }

    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'station_name' => 'required|string|max:255',
            'device_serial_number' => 'required|string|max:255|unique:device_registrations',
            'device_model' => 'required|string|max:255',
        ]);
    
        try {
            $response = Http::post('https://train.centricltd.co.ke/api/autocheckin/register_device', [
                'name' => $validated['station_name'],  // Changed from station_name to name
                'device_serial_number' => $validated['device_serial_number'],
                'device_model' => $validated['device_model'],
                'station_id' => '1', // Using station_id = 1 for testing
            ]);
    
            if ($response->successful()) {
                $data = $response->json();
                
                // Save device registration
                $device = DeviceRegistration::create([
                    'station_name' => $validated['station_name'],
                    'device_serial_number' => $validated['device_serial_number'],
                    'device_model' => $validated['device_model'],
                    'api_token' => $data['token'] ?? null,
                ]);
    
                return redirect()->route('display')->with('success', 'Device registered successfully!');
            } else {
                $errorMessage = $response->json()['message'] ?? $response->body();
                return back()->with('error', 'Failed to register device: ' . $errorMessage);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return back()->with('error', 'This device serial number is already registered.');
            }
            return back()->with('error', 'Database error occurred.');
        } catch (\Exception $e) {
            Log::error('Device registration failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while registering the device.');
        }
    }
}