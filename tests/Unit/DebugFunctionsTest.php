<?php

it('test debugging functions are not used', function () {
    expect(['dd', 'dump'])
        ->not
        ->toBeUsed();
});
