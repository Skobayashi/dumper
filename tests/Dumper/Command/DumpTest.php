<?php


use Dumper\Command\Dump;
use Dumper\Console\DumperApplication;
use Symfony\Component\Console\Tester\CommandTester;

class DumpTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DumperApplication
     **/
    private $app;


    /**
     * @return void
     **/
    public function setUp ()
    {
        $this->app = new DumperApplication();
        $this->app->add(new Dump());
    }


    /**
     * @return void
     **/
    public function tearDown ()
    {
        if (is_dir($this->dir)) {
            passthru('rm -rf '.$this->dir);
        }
    }


    /**
     * @test
     * @group holidaycsv-execute
     * @group holidaycsv
     **/
    public function 正常な処理 ()
    {
        $csv = '2015/01/07'.PHP_EOL.'2015/10/19';
        $this->_generateTestCsv($csv);

        $command = $this->app->find('holidaycsv');
        $tester  = new CommandTester($command);
        $tester->execute(array(
            'command'  => $command->getName(),
            'text_file_path' => $this->dir.'/holiday.txt'
        ));

        $result = '"Subject","Start Date","Start Time","End Date","End Time"'.PHP_EOL.
            '"三愛休日","2015/01/07",,"2015/01/07",'.PHP_EOL.
            '"三愛休日","2015/10/19",,"2015/10/19",';
        $this->assertEquals($result, file_get_contents($this->dir.'/holiday.csv'));
    }


    /**
     * テスト用のcsvを生成する
     *
     * @param  string $csv_data
     * @return void
     **/
    private function _generateTestCsv ($csv_data)
    {
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }

        file_put_contents($this->dir.'/holiday.txt', $csv_data);
    }
}

