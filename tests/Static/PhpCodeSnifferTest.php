<?php

namespace Static;

use Symfony\Component\Process\Process;

it('runs phpcs with verification for warnings and errors', function () {
    $command = './vendor/bin/phpcs';

    $process = Process::fromShellCommandline($command);
    $process->run();

    $output = $process->getOutput();

    expect($output)->toBe('', 'phpcs errors and/or warnings:');
});
