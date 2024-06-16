<?php

it('test debugging functions are not used', function () {
    expect(['dd', 'dump'])
        ->not
        ->toBeUsed();
});

it('test todo is not used', function () {
    expect('todo')
        ->not
        ->toBeUsed();
});
