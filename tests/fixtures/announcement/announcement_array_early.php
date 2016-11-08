<?php
use Sil\SspUtils\AnnouncementUtils;

// Start date is in the future

$future = new Datetime();
$future->add(new DateInterval('PT20S'));

return [
    'start_datetime' => $future->format(AnnouncementUtils::DATETIME_FORMAT),
    'announcement_text' => 'ANNOUNCEMENT_TEXT',
];