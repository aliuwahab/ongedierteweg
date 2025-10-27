<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Netherlands country
        $netherlands = Country::where('iso_code', 'NL')->first();

        if (!$netherlands) {
            throw new \Exception('Netherlands country not found. Please run CountrySeeder first.');
        }

        $provinces = [
            ['name' => 'Noord-Holland', 'population' => 2877909, 'latitude' => 52.5, 'longitude' => 4.8],
            ['name' => 'Zuid-Holland', 'population' => 3705625, 'latitude' => 52.0, 'longitude' => 4.6],
            ['name' => 'Utrecht', 'population' => 1353596, 'latitude' => 52.1, 'longitude' => 5.1],
            ['name' => 'Zeeland', 'population' => 383689, 'latitude' => 51.5, 'longitude' => 3.8],
            ['name' => 'Noord-Brabant', 'population' => 2562566, 'latitude' => 51.6, 'longitude' => 5.3],
            ['name' => 'Limburg', 'population' => 1117941, 'latitude' => 51.0, 'longitude' => 6.0],
            ['name' => 'Groningen', 'population' => 585866, 'latitude' => 53.2, 'longitude' => 6.6],
            ['name' => 'Friesland', 'population' => 649957, 'latitude' => 53.0, 'longitude' => 5.8],
            ['name' => 'Drenthe', 'population' => 493449, 'latitude' => 52.9, 'longitude' => 6.9],
            ['name' => 'Overijssel', 'population' => 1162215, 'latitude' => 52.5, 'longitude' => 6.3],
            ['name' => 'Gelderland', 'population' => 2085952, 'latitude' => 52.0, 'longitude' => 5.9],
            ['name' => 'Flevoland', 'population' => 423021, 'latitude' => 52.5, 'longitude' => 5.5],
        ];

        foreach ($provinces as $provinceData) {
            Province::create(array_merge($provinceData, [
                'country_id' => $netherlands->id
            ]));
        }
    }
}
