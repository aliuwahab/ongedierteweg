<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // Europe
            ['name' => 'Netherlands', 'iso_code' => 'NL', 'iso3_code' => 'NLD', 'phone_code' => '+31', 'is_active' => true],
            ['name' => 'Belgium', 'iso_code' => 'BE', 'iso3_code' => 'BEL', 'phone_code' => '+32', 'is_active' => false],
            ['name' => 'Germany', 'iso_code' => 'DE', 'iso3_code' => 'DEU', 'phone_code' => '+49', 'is_active' => false],
            ['name' => 'France', 'iso_code' => 'FR', 'iso3_code' => 'FRA', 'phone_code' => '+33', 'is_active' => false],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'iso3_code' => 'GBR', 'phone_code' => '+44', 'is_active' => false],
            ['name' => 'Spain', 'iso_code' => 'ES', 'iso3_code' => 'ESP', 'phone_code' => '+34', 'is_active' => false],
            ['name' => 'Italy', 'iso_code' => 'IT', 'iso3_code' => 'ITA', 'phone_code' => '+39', 'is_active' => false],
            ['name' => 'Portugal', 'iso_code' => 'PT', 'iso3_code' => 'PRT', 'phone_code' => '+351', 'is_active' => false],
            ['name' => 'Austria', 'iso_code' => 'AT', 'iso3_code' => 'AUT', 'phone_code' => '+43', 'is_active' => false],
            ['name' => 'Switzerland', 'iso_code' => 'CH', 'iso3_code' => 'CHE', 'phone_code' => '+41', 'is_active' => false],
            ['name' => 'Denmark', 'iso_code' => 'DK', 'iso3_code' => 'DNK', 'phone_code' => '+45', 'is_active' => false],
            ['name' => 'Sweden', 'iso_code' => 'SE', 'iso3_code' => 'SWE', 'phone_code' => '+46', 'is_active' => false],
            ['name' => 'Norway', 'iso_code' => 'NO', 'iso3_code' => 'NOR', 'phone_code' => '+47', 'is_active' => false],
            ['name' => 'Finland', 'iso_code' => 'FI', 'iso3_code' => 'FIN', 'phone_code' => '+358', 'is_active' => false],
            ['name' => 'Poland', 'iso_code' => 'PL', 'iso3_code' => 'POL', 'phone_code' => '+48', 'is_active' => false],
            ['name' => 'Czech Republic', 'iso_code' => 'CZ', 'iso3_code' => 'CZE', 'phone_code' => '+420', 'is_active' => false],
            ['name' => 'Ireland', 'iso_code' => 'IE', 'iso3_code' => 'IRL', 'phone_code' => '+353', 'is_active' => false],
            ['name' => 'Greece', 'iso_code' => 'GR', 'iso3_code' => 'GRC', 'phone_code' => '+30', 'is_active' => false],
            ['name' => 'Luxembourg', 'iso_code' => 'LU', 'iso3_code' => 'LUX', 'phone_code' => '+352', 'is_active' => false],

            // North America
            ['name' => 'United States', 'iso_code' => 'US', 'iso3_code' => 'USA', 'phone_code' => '+1', 'is_active' => false],
            ['name' => 'Canada', 'iso_code' => 'CA', 'iso3_code' => 'CAN', 'phone_code' => '+1', 'is_active' => false],
            ['name' => 'Mexico', 'iso_code' => 'MX', 'iso3_code' => 'MEX', 'phone_code' => '+52', 'is_active' => false],

            // Asia
            ['name' => 'China', 'iso_code' => 'CN', 'iso3_code' => 'CHN', 'phone_code' => '+86', 'is_active' => false],
            ['name' => 'Japan', 'iso_code' => 'JP', 'iso3_code' => 'JPN', 'phone_code' => '+81', 'is_active' => false],
            ['name' => 'South Korea', 'iso_code' => 'KR', 'iso3_code' => 'KOR', 'phone_code' => '+82', 'is_active' => false],
            ['name' => 'India', 'iso_code' => 'IN', 'iso3_code' => 'IND', 'phone_code' => '+91', 'is_active' => false],
            ['name' => 'Singapore', 'iso_code' => 'SG', 'iso3_code' => 'SGP', 'phone_code' => '+65', 'is_active' => false],
            ['name' => 'Thailand', 'iso_code' => 'TH', 'iso3_code' => 'THA', 'phone_code' => '+66', 'is_active' => false],
            ['name' => 'Malaysia', 'iso_code' => 'MY', 'iso3_code' => 'MYS', 'phone_code' => '+60', 'is_active' => false],
            ['name' => 'Indonesia', 'iso_code' => 'ID', 'iso3_code' => 'IDN', 'phone_code' => '+62', 'is_active' => false],
            ['name' => 'Philippines', 'iso_code' => 'PH', 'iso3_code' => 'PHL', 'phone_code' => '+63', 'is_active' => false],
            ['name' => 'Vietnam', 'iso_code' => 'VN', 'iso3_code' => 'VNM', 'phone_code' => '+84', 'is_active' => false],

            // Oceania
            ['name' => 'Australia', 'iso_code' => 'AU', 'iso3_code' => 'AUS', 'phone_code' => '+61', 'is_active' => false],
            ['name' => 'New Zealand', 'iso_code' => 'NZ', 'iso3_code' => 'NZL', 'phone_code' => '+64', 'is_active' => false],

            // Middle East
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'iso3_code' => 'ARE', 'phone_code' => '+971', 'is_active' => false],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SA', 'iso3_code' => 'SAU', 'phone_code' => '+966', 'is_active' => false],
            ['name' => 'Israel', 'iso_code' => 'IL', 'iso3_code' => 'ISR', 'phone_code' => '+972', 'is_active' => false],
            ['name' => 'Turkey', 'iso_code' => 'TR', 'iso3_code' => 'TUR', 'phone_code' => '+90', 'is_active' => false],

            // South America
            ['name' => 'Brazil', 'iso_code' => 'BR', 'iso3_code' => 'BRA', 'phone_code' => '+55', 'is_active' => false],
            ['name' => 'Argentina', 'iso_code' => 'AR', 'iso3_code' => 'ARG', 'phone_code' => '+54', 'is_active' => false],
            ['name' => 'Chile', 'iso_code' => 'CL', 'iso3_code' => 'CHL', 'phone_code' => '+56', 'is_active' => false],
            ['name' => 'Colombia', 'iso_code' => 'CO', 'iso3_code' => 'COL', 'phone_code' => '+57', 'is_active' => false],

            // Africa
            ['name' => 'South Africa', 'iso_code' => 'ZA', 'iso3_code' => 'ZAF', 'phone_code' => '+27', 'is_active' => false],
            ['name' => 'Egypt', 'iso_code' => 'EG', 'iso3_code' => 'EGY', 'phone_code' => '+20', 'is_active' => false],
            ['name' => 'Nigeria', 'iso_code' => 'NG', 'iso3_code' => 'NGA', 'phone_code' => '+234', 'is_active' => false],
            ['name' => 'Kenya', 'iso_code' => 'KE', 'iso3_code' => 'KEN', 'phone_code' => '+254', 'is_active' => false],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
