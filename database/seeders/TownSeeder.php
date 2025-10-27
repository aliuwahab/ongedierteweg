<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Town;
use Illuminate\Database\Seeder;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Major towns/cities for each Dutch province
        $towns = [
            'Groningen' => [
                ['name' => 'Groningen', 'population' => 233218],
                ['name' => 'Hoogezand-Sappemeer', 'population' => 34000],
                ['name' => 'Veendam', 'population' => 27387],
                ['name' => 'Stadskanaal', 'population' => 31843],
                ['name' => 'Winschoten', 'population' => 18207],
                ['name' => 'Delfzijl', 'population' => 24666],
                ['name' => 'Pekela', 'population' => 12199],
            ],
            'Friesland' => [
                ['name' => 'Leeuwarden', 'population' => 123107],
                ['name' => 'Sneek', 'population' => 33512],
                ['name' => 'Heerenveen', 'population' => 50494],
                ['name' => 'Drachten', 'population' => 45000],
                ['name' => 'Harlingen', 'population' => 15720],
                ['name' => 'Franeker', 'population' => 12781],
                ['name' => 'Dokkum', 'population' => 12675],
                ['name' => 'Bolsward', 'population' => 10123],
            ],
            'Drenthe' => [
                ['name' => 'Assen', 'population' => 67963],
                ['name' => 'Emmen', 'population' => 107055],
                ['name' => 'Hoogeveen', 'population' => 55697],
                ['name' => 'Meppel', 'population' => 33902],
                ['name' => 'Coevorden', 'population' => 35296],
                ['name' => 'Roden', 'population' => 19579],
            ],
            'Overijssel' => [
                ['name' => 'Zwolle', 'population' => 128831],
                ['name' => 'Enschede', 'population' => 158986],
                ['name' => 'Hengelo', 'population' => 80809],
                ['name' => 'Almelo', 'population' => 72725],
                ['name' => 'Deventer', 'population' => 100718],
                ['name' => 'Kampen', 'population' => 54340],
                ['name' => 'Oldenzaal', 'population' => 31830],
                ['name' => 'Hardenberg', 'population' => 60940],
            ],
            'Flevoland' => [
                ['name' => 'Almere', 'population' => 211840],
                ['name' => 'Lelystad', 'population' => 78619],
                ['name' => 'Dronten', 'population' => 41569],
                ['name' => 'Emmeloord', 'population' => 26055],
                ['name' => 'Zeewolde', 'population' => 22654],
            ],
            'Gelderland' => [
                ['name' => 'Arnhem', 'population' => 159265],
                ['name' => 'Nijmegen', 'population' => 176731],
                ['name' => 'Apeldoorn', 'population' => 163818],
                ['name' => 'Ede', 'population' => 117166],
                ['name' => 'Doetinchem', 'population' => 57555],
                ['name' => 'Harderwijk', 'population' => 48429],
                ['name' => 'Tiel', 'population' => 42161],
                ['name' => 'Wageningen', 'population' => 39673],
                ['name' => 'Zevenaar', 'population' => 43740],
                ['name' => 'Winterswijk', 'population' => 28865],
            ],
            'Utrecht' => [
                ['name' => 'Utrecht', 'population' => 357694],
                ['name' => 'Amersfoort', 'population' => 156286],
                ['name' => 'Veenendaal', 'population' => 66491],
                ['name' => 'Nieuwegein', 'population' => 63421],
                ['name' => 'Zeist', 'population' => 64932],
                ['name' => 'Houten', 'population' => 50146],
                ['name' => 'Soest', 'population' => 46194],
                ['name' => 'Woerden', 'population' => 52294],
            ],
            'Noord-Holland' => [
                ['name' => 'Amsterdam', 'population' => 872680],
                ['name' => 'Haarlem', 'population' => 162543],
                ['name' => 'Zaanstad', 'population' => 156711],
                ['name' => 'Haarlemmermeer', 'population' => 154205],
                ['name' => 'Alkmaar', 'population' => 109896],
                ['name' => 'Amstelveen', 'population' => 91691],
                ['name' => 'Purmerend', 'population' => 81233],
                ['name' => 'Hilversum', 'population' => 90883],
                ['name' => 'Hoorn', 'population' => 73232],
                ['name' => 'Den Helder', 'population' => 55604],
                ['name' => 'Velsen', 'population' => 68660],
            ],
            'Zuid-Holland' => [
                ['name' => 'Rotterdam', 'population' => 651446],
                ['name' => 'Den Haag', 'population' => 545163],
                ['name' => 'Leiden', 'population' => 125174],
                ['name' => 'Dordrecht', 'population' => 119260],
                ['name' => 'Zoetermeer', 'population' => 125283],
                ['name' => 'Delft', 'population' => 103659],
                ['name' => 'Gouda', 'population' => 73000],
                ['name' => 'Schiedam', 'population' => 78739],
                ['name' => 'Alphen aan den Rijn', 'population' => 111889],
                ['name' => 'Gorinchem', 'population' => 37009],
            ],
            'Zeeland' => [
                ['name' => 'Middelburg', 'population' => 48544],
                ['name' => 'Vlissingen', 'population' => 44365],
                ['name' => 'Goes', 'population' => 38080],
                ['name' => 'Terneuzen', 'population' => 54438],
                ['name' => 'Zierikzee', 'population' => 10513],
            ],
            'Noord-Brabant' => [
                ['name' => 'Eindhoven', 'population' => 234456],
                ['name' => 'Tilburg', 'population' => 219800],
                ['name' => 'Breda', 'population' => 184126],
                ['name' => 's-Hertogenbosch', 'population' => 155113],
                ['name' => 'Helmond', 'population' => 92432],
                ['name' => 'Oosterhout', 'population' => 55982],
                ['name' => 'Oss', 'population' => 91932],
                ['name' => 'Bergen op Zoom', 'population' => 67489],
                ['name' => 'Roosendaal', 'population' => 77032],
            ],
            'Limburg' => [
                ['name' => 'Maastricht', 'population' => 122378],
                ['name' => 'Venlo', 'population' => 101603],
                ['name' => 'Heerlen', 'population' => 86874],
                ['name' => 'Sittard-Geleen', 'population' => 91760],
                ['name' => 'Roermond', 'population' => 58254],
                ['name' => 'Weert', 'population' => 50107],
                ['name' => 'Kerkrade', 'population' => 45744],
            ],
        ];

        foreach ($towns as $provinceName => $provinceTowns) {
            $province = Province::where('name', $provinceName)->first();

            if ($province) {
                foreach ($provinceTowns as $townData) {
                    Town::create([
                        'name' => $townData['name'],
                        'province_id' => $province->id,
                        'population' => $townData['population'],
                    ]);
                }
            }
        }
    }
}
