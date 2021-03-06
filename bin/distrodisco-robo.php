<?php

use DistroDisco\DistroDisco;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Robo\Config\Config;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;

if (strpos(basename(__FILE__), 'phar')) {
  $root = __DIR__;
  require_once 'phar://distrodisco.phar/vendor/autoload.php';
}
else {
  if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $root = dirname(__DIR__);
    require_once dirname(__DIR__) . '/vendor/autoload.php';
  }
  elseif (file_exists(dirname(__DIR__) . '/../../autoload.php')) {
    $root = dirname(__DIR__) . '/../../..';
    require_once dirname(__DIR__) . '/../../autoload.php';
  }
  else {
    $root = __DIR__;
    require_once 'phar://distrodisco.phar/vendor/autoload.php';
  }
}

$config = new Config();
$loader = new YamlConfigLoader();
$processor = new ConfigProcessor();

$projectConfig = $root . '/distrodisco.yml';
$config->set('project_root', $root);

$processor->extend($loader->load(dirname(__DIR__) . '/default.distrodisco.yml'));
$processor->extend($loader->load($projectConfig));

$config->import($processor->export());
$config->set('config.project', $projectConfig);

$input = new ArgvInput($argv);
$output = new ConsoleOutput();
$app = new DistroDisco($config, $input, $output);
$statusCode = $app->run($input, $output);
exit($statusCode);
