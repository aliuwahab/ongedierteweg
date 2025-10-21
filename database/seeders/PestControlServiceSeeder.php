<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\PestControlService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PestControlServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Noord-Holland
            ['province' => 'Noord-Holland', 'name' => 'Amsterdam Pest Solutions', 'address' => 'Damrak 70, 1012 LM Amsterdam', 'phone' => '+31 20 123 4567', 'specialty' => 'Rodent Control', 'rating' => 4.8],
            ['province' => 'Noord-Holland', 'name' => 'Haarlem Ongedierte Bestrijding', 'address' => 'Grote Markt 2, 2011 RD Haarlem', 'phone' => '+31 23 543 2100', 'specialty' => 'Insect Control', 'rating' => 4.6],
            ['province' => 'Noord-Holland', 'name' => 'Noord Pest Experts', 'address' => 'Zuiderzeeweg 45, 1095 KG Amsterdam', 'phone' => '+31 20 694 3322', 'specialty' => 'General Pest', 'rating' => 4.7],
            
            // Zuid-Holland
            ['province' => 'Zuid-Holland', 'name' => 'Rotterdam Pest Control', 'address' => 'Coolsingel 40, 3011 AD Rotterdam', 'phone' => '+31 10 414 4188', 'specialty' => 'Commercial Pest', 'rating' => 4.9],
            ['province' => 'Zuid-Holland', 'name' => 'Den Haag Ongedierte Service', 'address' => 'Spui 70, 2511 BT Den Haag', 'phone' => '+31 70 353 5353', 'specialty' => 'Residential Pest', 'rating' => 4.5],
            ['province' => 'Zuid-Holland', 'name' => 'Delft Pest Management', 'address' => 'Markt 87, 2611 GW Delft', 'phone' => '+31 15 212 3456', 'specialty' => 'Bird Control', 'rating' => 4.4],
            
            // Utrecht
            ['province' => 'Utrecht', 'name' => 'Utrecht Centrale Pest', 'address' => 'Vredenburg 40, 3511 BD Utrecht', 'phone' => '+31 30 231 4142', 'specialty' => 'Termite Control', 'rating' => 4.7],
            ['province' => 'Utrecht', 'name' => 'Amersfoort Pest Solutions', 'address' => 'Langestraat 71, 3811 AB Amersfoort', 'phone' => '+31 33 461 1111', 'specialty' => 'Wasp Removal', 'rating' => 4.8],
            
            // Zeeland
            ['province' => 'Zeeland', 'name' => 'Middelburg Pest Control', 'address' => 'Markt 1, 4331 LJ Middelburg', 'phone' => '+31 118 612 345', 'specialty' => 'Agricultural Pest', 'rating' => 4.6],
            
            // Noord-Brabant
            ['province' => 'Noord-Brabant', 'name' => 'Eindhoven Pest Tech', 'address' => 'Stratumseind 45, 5611 EP Eindhoven', 'phone' => '+31 40 238 8000', 'specialty' => 'High-Tech Pest Control', 'rating' => 4.9],
            ['province' => 'Noord-Brabant', 'name' => 'Tilburg Ongedierte', 'address' => 'Heuvelstraat 35, 5038 AE Tilburg', 'phone' => '+31 13 545 4545', 'specialty' => 'General Pest', 'rating' => 4.5],
            
            // Limburg
            ['province' => 'Limburg', 'name' => 'Maastricht Pest Services', 'address' => 'Vrijthof 47, 6211 LE Maastricht', 'phone' => '+31 43 321 2345', 'specialty' => 'Historic Building Pest', 'rating' => 4.7],
            
            // Groningen
            ['province' => 'Groningen', 'name' => 'Groningen Noord Pest', 'address' => 'Grote Markt 1, 9712 HN Groningen', 'phone' => '+31 50 316 4911', 'specialty' => 'Student Housing Pest', 'rating' => 4.6],
            
            // Friesland
            ['province' => 'Friesland', 'name' => 'Leeuwarden Pest Control', 'address' => 'Nieuwestad 98, 8911 CX Leeuwarden', 'phone' => '+31 58 233 4455', 'specialty' => 'Farm Pest Control', 'rating' => 4.5],
            
            // Drenthe
            ['province' => 'Drenthe', 'name' => 'Assen Ongedierte Dienst', 'address' => 'Markt 1, 9401 JH Assen', 'phone' => '+31 592 366 555', 'specialty' => 'Rural Pest Control', 'rating' => 4.4],
            
            // Overijssel
            ['province' => 'Overijssel', 'name' => 'Zwolle Pest Management', 'address' => 'Grote Markt 20, 8011 LW Zwolle', 'phone' => '+31 38 421 5555', 'specialty' => 'Commercial Pest', 'rating' => 4.6],
            
            // Gelderland
            ['province' => 'Gelderland', 'name' => 'Arnhem Pest Solutions', 'address' => 'Korenmarkt 42, 6811 GV Arnhem', 'phone' => '+31 26 442 4242', 'specialty' => 'Urban Pest Control', 'rating' => 4.7],
            ['province' => 'Gelderland', 'name' => 'Nijmegen Ongedierte', 'address' => 'Grote Markt 1, 6511 KB Nijmegen', 'phone' => '+31 24 329 8111', 'specialty' => 'University Pest Control', 'rating' => 4.5],
            
            // Flevoland
            ['province' => 'Flevoland', 'name' => 'Almere Pest Control', 'address' => 'Stadhuisplein 1, 1315 HR Almere', 'phone' => '+31 36 539 9111', 'specialty' => 'New Development Pest', 'rating' => 4.8],
        ];

        foreach ($services as $service) {
            $province = Province::where('name', $service['province'])->first();
            if ($province) {
                PestControlService::create([
                    'province_id' => $province->id,
                    'name' => $service['name'],
                    'address' => $service['address'],
                    'phone' => $service['phone'],
                    'specialty' => $service['specialty'],
                    'rating' => $service['rating'],
                    'review_count' => rand(10, 100),
                    'is_active' => true,
                ]);
            }
        }
    }
}
