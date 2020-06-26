<?php

namespace Acquia\Console\ContentHub\Command;

use Acquia\Console\ContentHub\Command\Helpers\PlatformCmdOutputFormatterTrait;
use Acquia\Console\ContentHub\Command\Helpers\PlatformCommandExecutionTrait;
use EclipseGc\CommonConsole\Platform\PlatformCommandTrait;
use EclipseGc\CommonConsole\PlatformCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentHubClientCompare.
 *
 * @package Acquia\Console\ContentHub\Command
 */
class ContentHubClientCompare extends Command implements PlatformCommandInterface {

  use PlatformCommandTrait;
  use PlatformCmdOutputFormatterTrait;
  use PlatformCommandExecutionTrait;

  /**
   * {@inheritdoc}
   */
  protected static $defaultName = 'ach:audit:client-compare';

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setDescription('Compare the counts of platform sites and subscription clients.');
    $this->setAliases(['ach-cl-diff']);
  }

  /**
   * {@inheritdoc}
   */
  public static function getExpectedPlatformOptions(): array {
    return [
      'source' => PlatformCommandInterface::ANY_PLATFORM,
    ];
  }

  /**
   * ContentHubClientCompare constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param string|NULL $name
   *   The name of this command.
   */
  public function __construct(EventDispatcherInterface $dispatcher, string $name = NULL) {
    parent::__construct();
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Execution in progress...');
    /** @var \Acquia\Console\Cloud\Platform\AcquiaCloudPlatform $platform */
    $platform = $this->getPlatform('source');
    $sites_count = count($platform->getPlatformSites());

    $raw = $this->runWithMemoryOutput(ContentHubAuditClients::getDefaultName(), [
        '--count',
      ]);

    $lines = explode(PHP_EOL, trim($raw));
    foreach ($lines as $line) {
      $data = $this->fromJson($line, $output);
      if (!$data) {
        continue;
      }

      if ($sites_count !== $data->count) {
        $output->writeln("<error>You have $sites_count sites in your platform configuration and {$data->count} clients in your subscription.</error>");
        $output->writeln('Please review your configuration!');
      } else {
        $output->writeln('Sites count and clients count are equal');
      }
    }

    return 0;
  }

}
