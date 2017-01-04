<?php

namespace BitolaCo\MemberClicks;

class Event extends ClientObj
{

    public $eventId;
    public $orgId;
    public $name;
    public $preview;
    public $description;
    public $startDate;
    public $endDate;
    public $location;
    public $contactName;
    public $contactPhone;
    public $displayStartDate;
    public $displayEndDate;
    public $timeZoneId;
    public $mapLocation;
    public $submittedByName;
    public $approved;
    public $eventGroup;
    public $recurringType;
    public $numberOfIntervals;
    public $monthOccurrence;
    public $dayOfWeek;
    public $recurringEndType;
    public $recurringEndNumber;
    public $recurringEndDate;
    public $dayOfMonth;

    public function load(): string
    {
        if (!$this->client) {
            return 'Not initialized';
        }
        if (!$this->eventId) {
            return 'No event id';
        }
        list($event, $err) = $this->client->getEvent($this->eventId);
        if ($err) {
            return $err;
        }
        $this->copyAttrs((array) $event);
        return '';
    }

    public static function all(array $params = []): array
    {
        if (!$this->client) {
            return 'Not initialized';
        }
        return $this->client->getEvents($params);
    }

}
