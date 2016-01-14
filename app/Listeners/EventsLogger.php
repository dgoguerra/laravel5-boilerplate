<?php

namespace App\Listeners;

use App\Events\Event;
use Illuminate\Config\Repository;
use Illuminate\Log\Writer;

class EventsLogger
{
    protected $config;

    protected $logger;

    public function __construct(Repository $config, Writer $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    protected function logEvent($eventName, array $data = [])
    {
        if ($this->config->get('app.debug') !== true) {
            return;
        }

        $this->logger->debug('Event fired', array_merge(['event_name' => $eventName], $data));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('auth.login', function($user) {
            $this->logEvent('auth.login', ['user_id' => $user->id, 'user_email' => $user->email]);
        });

        $events->listen('auth.logout', function($user) {
            $this->logEvent('auth.logout', ['user_id' => $user->id, 'user_email' => $user->email]);
        });

        $events->listen('mailer.sending', function($message) {
            $this->logEvent('mailer.sending', [
                'subject' => $message->getSubject(),
                'to' => $message->getTo(),
                'from' => $message->getFrom()
            ]);
        });

        $events->listen('App\Events\*', function($event) {
            $this->logEvent(get_class($event));
        });
    }
}
