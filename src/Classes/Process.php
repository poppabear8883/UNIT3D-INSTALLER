<?php

namespace App\Classes;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{

    /**
     * @var SymfonyStyle $io
     */
    private $io;

    /**
     * @var bool $debug
     */
    private $debug = false;

    /**
     * Process constructor.
     * @param SymfonyStyle $io
     */
    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * Executes a new Process instance
     *
     * @param string $cmd
     * @return string
     */
    public function execute($command, array $input = null, $force = false, $timeout = 3600, $cwd = null, array $env = null)
    {

        $this->io->writeln("\n<fg=cyan>$command</>");

        $process = new SymfonyProcess($command, $cwd, $env, null, $timeout);
        $process->setIdleTimeout(900);

        $inputStream = null;
        if ($input !== null && is_array($input)) {
            $inputStream = new InputStream();
            $process->setInput($inputStream);

            !$this->debug ?: $this->io->writeln('[debug] Pty is on');
            $process->setPty(true);
        }

        $bar = $this->progressStart();

        $process->start();

        $process->wait(function ($type, $buffer) use ($bar, $input, $inputStream) {
            !$this->debug ?: $this->io->writeln("[debug] $buffer");

            if ($input !== null && is_array($input)) {

                $last = null;
                foreach($input as $expect => $send) {
                    if (str_contains($buffer, $expect) && $expect !== $last) {
                        $inputStream->write($send . "\n");
                        $last = $expect;
                    }

                    usleep(5000);
                }

            }

            $bar->advance();
            usleep(200000);
        });

        $this->progressStop($bar);
        $process->stop();

        if (!$process->isSuccessful()) {
            if (!$force) {
                $this->io->error($process->getErrorOutput());
                die();
            }

            $this->io->writeln("\n<fg=red>[Warning]</> " . $process->getErrorOutput());
        }

        return $process;
    }

    /**
     * @return ProgressBar
     */
    protected function progressStart()
    {
        $bar = $this->io->createProgressBar();
        $bar->setBarCharacter('<fg=magenta>=</>');
        $bar->setFormat('[%bar%] (<fg=cyan>%message%</>)');
        $bar->setMessage('Please Wait ...');
        //$bar->setRedrawFrequency(20); todo: may be useful for platforms like CentOS
        $bar->start();

        return $bar;
    }

    /**
     * @param $bar
     */
    protected function progressStop(ProgressBar $bar)
    {
        $bar->setMessage("<fg=green>Done!</>");
        $bar->finish();
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }
}
