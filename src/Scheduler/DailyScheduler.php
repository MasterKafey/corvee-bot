<?php

namespace App\Scheduler;

use App\MessageHandler\Message\ExecuteCommandMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
readonly class DailyScheduler implements ScheduleProviderInterface
{
    public function __construct(
        private int    $jeanHourCron,
        private int    $anneSophieHourCron,
        private string $codeurCron
    )
    {

    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::cron("0 $this->jeanHourCron * * *", new ExecuteCommandMessage('app:discord:send-today-corvees', ['user' => 'Jean'])),
            RecurringMessage::cron("0 $this->anneSophieHourCron * * *", new ExecuteCommandMessage('app:discord:send-today-corvees', ['user' => 'Anne-Sophie'])),
            RecurringMessage::cron("0 0 * * *", new ExecuteCommandMessage('app:corvee:clear')),
            RecurringMessage::cron("* * * * *", new ExecuteCommandMessage('app:supermarket-item:clear')),
            RecurringMessage::cron($this->codeurCron, new ExecuteCommandMessage('app:codeur:send-offer'))
        );
    }
}
