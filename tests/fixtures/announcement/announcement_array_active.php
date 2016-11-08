<?php
use Sil\SspUtils\AnnouncementUtils;

// Start date in the past and an end date in the future.

$past = new Datetime();
$past->sub(new DateInterval('PT20S'));
$future = new Datetime();
$future->add(new DateInterval('PT20S'));

$dt_format = AnnouncementUtils::DATETIME_FORMAT;

return [
    'start_datetime' => $past->format($dt_format),
    'end_datetime' => $future->format($dt_format),
    'announcement_text' => 'ANNOUNCEMENT_ACTIVE',
];