<?php


namespace Dumper\Console;

use Symfony\Component\Console\Application;
use Dumper\Command\Dump;

class DumperApplication extends Application
{

    /**
     * コマンドの初期化を行う
     *
     * @return void
     **/
    public function __construct ()
    {
        parent::__construct('Dumper Commands --', VERSION);

        $this->add(new Dump());
    }
}

