<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to attendee of the event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])->get();

        $eventsCount = $events->count();
        $eventLabel = Str::plural('event', $eventsCount);
        $this->info("Found {$eventsCount} {$eventLabel}");

        $events->each(fn ($event) => $event->attendees->each(
            fn ($attendee) => $this->info("Notifying the user {$attendee->user_id}")));
        $this->info('Reminder notifications sent successfully');
    }
}
