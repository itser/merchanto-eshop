<?php

test('application health check', function () {
    $this->get('/up')->assertOk();
});
