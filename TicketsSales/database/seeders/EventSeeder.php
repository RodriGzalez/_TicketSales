<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'title' => 'Graduación Facultad de Ingeniería 2026',
            'description' => 'Gran ceremonia de graduación de la generación 2022-2026. Acompaña a los futuros ingenieros en su gran noche.',
            'date' => '2026-12-15 18:00:00',
            'location' => 'Centro de Convenciones Siglo XXI',
            'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop',
            'total_capacity' => 1500,
            'max_tickets_per_user' => 5,
            'reservation_expiry_time' => 10,
            'status' => 'active',
        ]);

        Event::create([
            'title' => 'Ceremonia de Graduación Preparatoria UNITEC',
            'description' => 'Celebra el cierre de esta gran etapa estudiantil con amigos, familiares y una cena inolvidable.',
            'date' => '2026-12-20 17:00:00',
            'location' => 'Auditorio Nacional',
            'image_url' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=1000&auto=format&fit=crop',
            'total_capacity' => 800,
            'max_tickets_per_user' => 4,
            'reservation_expiry_time' => 10,
            'status' => 'active',
        ]);
    }
}
