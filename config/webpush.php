<?php

return [
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@bbpom.go.id'),
        'public_key' => env('VAPID_PUBLIC_KEY', 'BFwvB_Ekk2EcnNVyZ0lwsMP42sQkZf-tCuvVuQYendZG8JfaRU7X9twKu0lIArqsa7A2_tu8_SX66UV8KV_JzvU'),
        'private_key' => env('VAPID_PRIVATE_KEY', 'tm9qYK_tyOeFpXVGmZRxKV8ayyvtzFh3dfKIzCfqOGM'),
    ],
];
