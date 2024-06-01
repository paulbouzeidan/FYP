<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\services;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $services = [
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Car Detailing",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Car",
                'price' => 50,
                'isEmergency' => false,
                
            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Oil Change",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Car",
                'price' => 20,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Personal Driver",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Transportation",
                'price' => 20,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Personal Taxi",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Transportation",
                'price' => 20,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Paperwork",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Paperwork",
                'price' => 20,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Paperwork for Car",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Paperwork",
                'price' => 35,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Grocery Store Delivery",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Delivery",
                'price' => 20,
                'isEmergency' => false,

            ],
            [
                'image' => '../assets/images/service-image.png',
                'service_name' => "Package Delivery",
                'service_description' => "This service is made to help you make car detailing for your car while you are relaxed and doing your thing without worrying about it",
                'category' => "Delivery",
                'price' => 30,
                'isEmergency' => false,

            ],
        ];

        // Loop through the services array and insert each service into the database
        foreach ($services as $serviceData) {
            services::create($serviceData);
        }
    }
}
