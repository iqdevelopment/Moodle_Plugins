<?php


$tasks = array(
    array(
        'classname' => 'local_custom_notification\task\custom_notification_send',
        'blocking' => 0,
        'minute' => '*/2',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),

    array(
        'classname' => 'local_custom_notification\task\custom_notification_fill',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);