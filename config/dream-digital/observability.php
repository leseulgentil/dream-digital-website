<?php

return [
    'request_id_header' => env('DD_REQUEST_ID_HEADER', 'X-Request-Id'),
    'slow_request_ms' => (int) env('DD_SLOW_REQUEST_MS', 1000),
];
