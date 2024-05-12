<?php

use Symfony\Component\Process\Process;

it('runs eslint with verification for warnings and errors', function () {
    $command = 'npx eslint --max-warnings=0 --ext .js,.vue resources/js';

    $process = Process::fromShellCommandline($command);
    $process->run();

    $output = $process->getOutput();

    expect($output)->toBe('', 'eslint errors and/or warnings:');
});
