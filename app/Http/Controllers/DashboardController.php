<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'artworks' => [
                ['id' => 'PV-1048', 'title' => 'Neon Solitude', 'artist' => 'Maya Chen', 'category' => '2D', 'price' => 25, 'status' => 'Active', 'theme' => 'neon'],
                ['id' => 'PV-1047', 'title' => 'Chromatic Drift', 'artist' => 'Jon Bell', 'category' => '3D', 'price' => 32, 'status' => 'Active', 'theme' => 'drift'],
                ['id' => 'PV-1046', 'title' => 'Quiet Geometry', 'artist' => 'Ana Silva', 'category' => 'Vector', 'price' => 18, 'status' => 'In review', 'theme' => 'geometry'],
                ['id' => 'PV-1045', 'title' => 'Digital Bloom', 'artist' => 'Mika Ross', 'category' => '2D', 'price' => 29, 'status' => 'Active', 'theme' => 'bloom'],
                ['id' => 'PV-1044', 'title' => 'Midnight City', 'artist' => 'Theo Park', 'category' => 'Pixel Art', 'price' => 21, 'status' => 'Active', 'theme' => 'city'],
                ['id' => 'PV-1043', 'title' => 'Soft Machines', 'artist' => 'Nia Gray', 'category' => '3D', 'price' => 38, 'status' => 'Active', 'theme' => 'machines'],
            ],
        ]);
    }
}
