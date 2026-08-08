<?php

namespace Database\Seeders;

use App\Models\Laptop;
use Illuminate\Database\Seeder;

class LaptopDemoSeeder extends Seeder
{
    public function run(): void
    {
        Laptop::firstOrCreate(
            ['reference' => 'PC-0231'],
            ['brand' => 'Dell', 'model' => 'Latitude 5480', 'serial_number' => 'DL5480-0001', 'status' => 'attribue']
        );

        Laptop::firstOrCreate(
            ['reference' => 'PC-0232'],
            ['brand' => 'HP', 'model' => 'ProBook 440', 'serial_number' => 'HP440-0002', 'status' => 'disponible']
        );

        Laptop::firstOrCreate(
            ['reference' => 'PC-0233'],
            ['brand' => 'Lenovo', 'model' => 'ThinkPad E14', 'serial_number' => 'LNV-E14-0003', 'status' => 'maintenance']
        );
    }
}
