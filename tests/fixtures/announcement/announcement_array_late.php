<?php
use Sil\SspUtils\AnnouncementUtils;

// end date is in the past

$past = new Datetime();
$past->sub(new DateInterval('PT20S'));

return [
    'end_datetime' => $past->format(AnnouncementUtils::DATETIME_FORMAT),
    'announcement_text' => 'ANNOUNCEMENT_LATE',
];