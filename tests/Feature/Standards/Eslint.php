<?php

namespace Standards;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class Eslint extends TestCase
{
    /** @test */
    public function it_runs_eslint_with_verification_for_warnings_and_errors(): void
    {
        $command = 'npx eslint --max-warnings=0 --ext .js,.vue resources/js';

        $process = Process::fromShellCommandline($command);
        $process->run();

        $output = $process->getOutput();

        expect($output)->toBe('', 'eslint errors and/or warnings:');
    }
}
