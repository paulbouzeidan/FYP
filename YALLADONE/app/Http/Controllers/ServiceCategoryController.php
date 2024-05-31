<?php

namespace App\Http\Controllers;
use App\Models\services;

use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function getCarServices()
    {
        try {
            $service = new services();
            $services = $service->getCarServices();
            return response()->json($services, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // Function to get transportation services
    public function getTransportationServices()
    {
        try {
            $service = new services();
            $services = $service->getTransportationServices();
            return response()->json($services, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // Function to get paperwork services
    public function getPaperworkServices()
    {
        try {
            $service = new services();
            $services = $service->getPaperworkServices();
            return response()->json($services, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // Function to get delivery services
    public function getDeliveryServices()
    {
        try {
            $service = new services();
            $services = $service->getDeliveryServices();
            return response()->json($services, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
