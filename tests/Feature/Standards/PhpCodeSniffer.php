<?php

namespace Standards;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class PhpCodeSniffer extends TestCase
{
    /** @test */
    public function it_runs_phpcs_with_verification_for_warnings_and_errors(): void
    {
        $command = './vendor/bin/phpcs';

        $process = Process::fromShellCommandline($command);
        $process->run();

        $output = $process->getOutput();

        expect($output)->toBe('', 'phpcs errors and/or warnings:');
    }
}
