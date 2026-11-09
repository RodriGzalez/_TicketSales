<?php

namespace App\Observers;

use App\Models\Event;
use App\Traits\LogsAdminActivity;

class EventObserver
{
    use LogsAdminActivity;

    public function created(Event $event): void
    {
        self::logActivity(
            'CREATED',
            'Eventos',
            "Creó el evento '{$event->title}'",
            $event->getAttributes()
        );
    }

    public function updated(Event $event): void
    {
        self::logActivity(
            'UPDATED',
            'Eventos',
            "Actualizó el evento '{$event->title}'",
            [
                'old' => $event->getOriginal(),
                'new' => $event->getChanges(),
            ]
        );
    }

    public function deleted(Event $event): void
    {
        self::logActivity(
            'DELETED',
            'Eventos',
            "Eliminó el evento '{$event->title}'"
        );
    }
}