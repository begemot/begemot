<?php

class ParseCommand extends CConsoleCommand
{
    public function run()
    {

        $n = new JobManager();

        $n->runAll();

    }

}
