<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\TicketType;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $eventsData = [
        [
            'title' => 'Graduación Facultad de Ingeniería 2026',
            'description' => 'Gran ceremonia de graduación de la generación 2022-2026. Acompaña a los futuros ingenieros en su gran noche.',
            'date' => '2026-12-15 18:00:00',
            'location' => 'Centro de Convenciones Siglo XXI',
            'image_url' => 'https://images.unsplash.com/photo-1523050854057-8df90110c9f1?q=80&w=1000&auto=format&fit=crop',
            'total_capacity' => 1500,
            'max_tickets_per_user' => 5,
            'reservation_expiry_time' => 10,
            'status' => 'active',
        ],

        [
            'title' => 'Ceremonia de Graduación Preparatoria UNITEC',
            'description' => 'Celebra el cierre de esta gran etapa estudiantil con amigos, familiares y una cena inolvidable.',
            'date' => '2026-12-20 17:00:00',
            'location' => 'Auditorio Nacional',
            'image_url' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=1000&auto=format&fit=crop',
            'total_capacity' => 800,
            'max_tickets_per_user' => 4,
            'reservation_expiry_time' => 10,
            'status' => 'active',
        ],
        
        [
            'title' => 'Graduación Facultad de Ingeniería 2026',
            'description' => 'Gran ceremonia de graduación de la generación 2022-2026. Acompaña a los futuros ingenieros en su gran noche con cena gourmet, grupo en vivo y alfombra roja.',
            'date' => '2026-12-15 18:00:00',
            'location' => 'Centro de Convenciones Siglo XXI',
            'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop',
            'total_capacity' => 1500,
            'max_tickets_per_user' => 5,
            'reservation_expiry_time' => 10,
            'status' => 'active',
        ]
    ];
       foreach ($eventsData as $data) {
            $event = Event::create($data);

            // Crear dinámicamente los tipos de boletos para ESTE evento
            TicketType::create([
                'event_id' => $event->id,
                'name' => 'Boleto General / Adulto',
                'price' => 850.00,
                'stock' => intval($event->total_capacity * 0.7),
            ]);

            TicketType::create([
                'event_id' => $event->id,
                'name' => 'Boleto Niños (Menores de 12 años)',
                'price' => 450.00,
                'stock' => intval($event->total_capacity * 0.3),
            ]);
        }
    }
}
