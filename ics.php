<?php
require_once __DIR__ . '/includes/data.php';
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="event.ics"');

$id = isset($_GET['id']) ? $_GET['id'] : null;
$e  = $id ? find_event_by_id($id) : null;

if (!$e) { echo "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nEND:VCALENDAR"; exit; }

$title = $e['title'] ?? 'Event';
$desc  = $e['description'] ?? '';
$loc   = $e['location'] ?? '';
$dt    = preg_replace('/[^0-9\-]/','', $e['event_date'] ?? '');
$dt    = $dt ? (new DateTime($dt.' 18:00:00'))->format('Ymd\THis') : date('Ymd\THis');
$uid   = uniqid('evt-');

echo "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//City Events//AR//EN\r\n";
echo "BEGIN:VEVENT\r\n";
echo "UID:$uid\r\n";
echo "DTSTAMP:".gmdate('Ymd\THis\Z')."\r\n";
echo "DTSTART:$dt\r\n";
echo "DTEND:".(new DateTime($e['event_date'].' 20:00:00'))->format('Ymd\THis')."\r\n";
echo "SUMMARY:".str_replace(["\r","\n"],' ', $title)."\r\n";
echo "DESCRIPTION:".str_replace(["\r","\n"],' ', $desc)."\r\n";
echo "LOCATION:".str_replace(["\r","\n"],' ', $loc)."\r\n";
echo "END:VEVENT\r\nEND:VCALENDAR";
