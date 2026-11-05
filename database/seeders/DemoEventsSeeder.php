<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoEventsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $organizer = Organizer::firstOrCreate(
            ['slug' => 'organisateur-demo'],
            ['user_id' => $user->id, 'name' => 'Organisateur démo', 'status' => 'approved']
        );

        $events = [
            ['Nuit Gospel', 'Esplanade centrale', 'Cotonou', 21, 18, [['Standard', 3000, 500], ['VIP', 10000, 100]]],
            ['Cinéma sous les étoiles', 'Jardin des Arts', 'Porto-Novo', 35, 19, [['Entrée', 2000, 300]]],
            ['Afro Sunset Party', 'Plage de Fidjrossè', 'Cotonou', 49, 17, [['Early bird', 5000, 200], ['Standard', 7000, 400]]],
        ];

        foreach ($events as [$title, $venue, $city, $days, $hour, $types]) {
            $event = Event::firstOrCreate(['slug' => Str::slug($title)], [
                'organizer_id' => $organizer->id,
                'title' => $title,
                'description' => 'Événement de démonstration.',
                'venue' => $venue,
                'city' => $city,
                'starts_at' => now()->addDays($days)->setTime($hour, 0),
                'status' => 'published',
            ]);

            if ($event->wasRecentlyCreated) {
                foreach ($types as [$name, $price, $quantity]) {
                    $event->ticketTypes()->create(compact('name', 'price', 'quantity'));
                }
            }
        }
    }
}