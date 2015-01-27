Dumper [![Build Status](https://travis-ci.org/Skobayashi/Dumper.svg?branch=master)](https://travis-ci.org/Skobayashi/ToolName) [![Coverage Status](https://coveralls.io/repos/Skobayashi/Dumper/badge.png?branch=master)](https://coveralls.io/r/Skobayashi/ToolName?branch=master)
====

Dumper is a database backup tool.

## Description

Dumper will dump the data from the database, and upload to AmazonS3.  
It is a good idea to fill as a task every day with Cron.

## Requirement

PHP 5.4+  
and Composer.

## Usage

It is simple operation.

```
$ bin/dumper dump hostname
```

## Install

```
$ composer.phar install
```

and set aws configuration.

```
$ export AWS_ACCESS_KEY_ID="your_aws_access_key"
$ export AWS_SECRET_ACCESS_KEY="your_aws_secret_access_key"
$ export AWS_DEFAULT_REGION="your_region"
```

## License

[MIT](https://github.com/app2641/Dumper/blob/master/LICENSE)

## Author

[Skobayashi](https://github.com/Skobayashi)
