<?php


namespace Dumper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

use Acm\Acm;
use Guzzle\Http\EntityBody;

class Dump extends Command
{

    /**
     * @var string
     **/
    private $config_path = 'data/setting.yml';


    /**
     * @return void
     **/
    protected function configure ()
    {
        $this->setName('dump')
            ->setDescription('データベースのバックアップを行う');

        // 引数の記載
        $this->addArgument(
            'hostname',
            InputArgument::REQUIRED,
            'ホスト名の指定'
        );
    }


    /**
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     **/
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        if (! file_exists(ROOT.'/'.$this->config_path)) {
            throw new \Exception('設定ファイルを生成してください');
        }

        $config = Yaml::parse(ROOT.'/'.$this->config_path);
        $hostname = $input->getArgument('hostname');

        // aws configure
        if (! getenv('AWS_ACCESS_KEY_ID')) {
            putenv(sprintf('AWS_ACCESS_KEY_ID=%s', $config['aws']['access_key']));
        }
        if (! getenv('AWS_SECRET_ACCESS_KEY')) {
            putenv(sprintf('AWS_SECRET_ACCESS_KEY=%s', $config['aws']['secret_key']));
        }

        if (! isset($config['hosts'][$hostname])) {
            throw new InvalidArgumentException('ホスト名が間違っています');
        }

        $host = $config['hosts'][$hostname];
        foreach ($host['databases'] as $database) {
            try {
                $dump_file = sprintf('%s-%s.gz', $database, date('Ymd'));
                $dump_path = '/tmp/'.$dump_file;
                $command = sprintf(
                    'mysqldump --skip-lock-tables -u %s --password="%s" %s | gzip -9 > %s',
                    $host['user'],
                    $host['pass'],
                    $database,
                    $dump_path
                );
                passthru($command);

                if (! file_exists($dump_path)) {
                    throw new \RuntimeException($database.'のdumpに失敗しました');
                }

                // s3
                $s3 = Acm::getS3(['region' => $config['aws']['s3']['region']]);
                $path = $hostname.'/'.$database.'/'.$dump_file;

                $s3->putObject([
                    'Bucket' => $config['aws']['s3']['bucket'],
                    'Key' => $path,
                    'Body' => EntityBody::factory(fopen($dump_path, 'r'))
                ]);

                // glacier
                $s3 = Acm::getS3(['region' => $config['aws']['glacier']['region']]);
                $s3->putObject([
                    'Bucket' => $config['aws']['glacier']['bucket'],
                    'Key' => $path,
                    'Body' => EntityBody::factory(fopen($dump_path, 'r'))
                ]);

                unlink($dump_path);

            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}

