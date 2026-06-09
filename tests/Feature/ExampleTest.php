<?php

use function Pest\Laravel\get;

test('application health check', function () {
    get('/up')->assertOk();
});
