<?php

namespace App\Traits;

trait ConsoleTools
{

    /**
     * Formats a warning output
     *
     * @param string $msg
     */
    protected function warning($msg)
    {
        $this->io->writeln("<bg=white;fg=yellow>[Warning] $msg</>\n");
    }

    /**
     * Returns the installer error and exits the installer
     *
     * @param array|string $error
     */
    protected function throwError($error = 'Unknown Error ...')
    {
        $this->io->writeln("<fg=red>$error</>");
        exit(1);
    }

    protected function dump($var)
    {
        $this->io->writeln('<fg=red>---VAR DUMP---');
        var_dump($var);
        $this->io->writeln('--------------</>');
    }

    /**
     * Displays a the intro
     */
    protected function displayIntro()
    {
        $stub = file_get_contents(__DIR__ . '/../Resources/intro.stub');

        $this->io->text($stub);
    }

    /**
     * Writes a seperator
     */
    protected function seperator()
    {
        $this->io->writeln(str_repeat('=', 80));
    }

    /**
     * Formats a header
     *
     * @param string $text
     */
    protected function head($text)
    {
        if ($text !== null) {
            $this->io->writeln("\n<fg=blue>" . str_repeat('=', 80));
            $this->io->writeln('    ' . $text . str_repeat(' ', (76 - strlen($text))));
            $this->io->writeln(str_repeat('=', 80) . "</>\n");
        }
    }

    protected function success()
    {
        $this->io->writeln("\n<fg=white;bg=green>[OK] Done!" . str_repeat(' ', 70) . "</>");
    }

    protected function question($question, $default = '')
    {
        do {
            $answer = $this->io->ask($question, $default);

            $valid = ($answer !== '' && strpos($answer, ' ') === false);

            if (!$valid) {
                $this->warning('Cannot be empty or contain spaces!');
            }

        } while (!$valid);

        return trim($answer);
    }
}