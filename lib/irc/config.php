<?php
return array(
    'server'   => 'irc.rirnef.net',
    'port'     => 6667,
    'name'     => 'Tachikoma Bot',
    'password' => 'g78f90ds',
    'nick'     => 'Tachikoma',
    'channels' => array(
        '#dmstream',
    ),
    'timezone' => 'America/Chicago',
    'max_reconnects' => 3,
    'prefix'         => '!',
    'log' => array(
        'file'       => 'log', // No file extension!
        'dir'        => ROOT_DIR . '/logs',

        // Set this if you want only output from specific channel(s) to show up.
        // This will not log any other output, so this is not useful for debugging.
        // This is particularly useful if you use public logs.
        // Can contain multiple channels.
        //'filter'     => array('#wildphp'),
    ),
    'commands'       => array(
        'Command\Say'     => array(),
        'Command\Weather' => array(
            'yahooKey' => 'dj0yJmk9MEVNRzBEd2poOFlaJmQ9WVdrOVRtUnlZMHRvTm5FbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD02Ng--',
        ),
        'Command\Joke'    => array(),
        'Command\Ip'      => array(),
        'Command\Imdb'    => array(),
        'Command\Poke'    => array(),
        'Command\Topic'   => array(),
        'Command\Join'    => array(),
        'Command\Part'    => array(),
        'Command\Timeout' => array(),
        'Command\Quit'    => array(),
        'Command\Restart' => array(),
        'Command\Help'    => array(),
		'Command\Update'  => array(),
    ),
    'listeners' => array(
        'Listener\Joins' => array(),
        'Listener\Youtube' => array(),
    ),
    'hosts' => array(
        'len@rirnef.net',
    ),
);
