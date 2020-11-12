<?php

namespace Acquia\Console\ContentHub\Command\ServiceSnapshots;

use Acquia\Console\ContentHub\Command\Helpers\ContentHubDeleteSnapshotHelper;
use Acquia\Console\ContentHub\Command\Helpers\PlatformCmdOutputFormatterTrait;
use Acquia\Console\ContentHub\Command\Helpers\PlatformCommandExecutionTrait;
use EclipseGc\CommonConsole\Platform\PlatformCommandTrait;
use EclipseGc\CommonConsole\PlatformCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentHubDeleteSnapshot.
 *
 * @package Acquia\Console\ContentHub\Command\ServiceSnapshots
 */
class ContentHubDeleteSnapshot extends Command implements PlatformCommandInterface {

  use PlatformCommandTrait;
  use PlatformCommandExecutionTrait;
  use PlatformCmdOutputFormatterTrait;

  /**
   * {@inheritdoc}
   */
  protected static $defaultName = 'ach:delete-snapshot';

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setDescription('Delete Acquia Content Hub snapshots.')
      ->setAliases(['ach-ds']);
  }

  /**
   * {@inheritdoc}
   */
  public static function getExpectedPlatformOptions(): array {
    return ['source' => PlatformCommandInterface::ANY_PLATFORM];
  }

  /**
   * ContentHubSubscriptionSet constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The dispatcher service.
   * @param string|null $name
   *   The name of the command.
   */
  public function __construct(EventDispatcherInterface $dispatcher, string $name = NULL) {
    parent::__construct($name);
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $snapshots = $this->listSnapshots($output);

    $helper = $this->getHelper('question');
    $question = new ChoiceQuestion('Select snapshot you want to delete.', $snapshots);
    $select_snapshot = $helper->ask($input, $output, $question);

    $delete_snapshot = $this->runWithMemoryOutput(ContentHubDeleteSnapshotHelper::getDefaultName(), ['--name' => $select_snapshot]);
    if ($delete_snapshot->getReturnCode()) {
      $output->writeln(sprintf('<error>Could not delete snapshot: %s</error>', $select_snapshot));
      return 1;
    }
    $output->writeln(sprintf('<info>Snapshot deleted successfully: %s</info>', $select_snapshot));
    return 0;
  }

  /**
   * List snapshots.
   *
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The output.
   *
   * @return array
   *   Snapshots list.
   */
  protected function listSnapshots($output) {
    $raw = $this->runWithMemoryOutput(ContentHubGetSnapshots::getDefaultName(), ['--list' => TRUE]);
    if ($raw->getReturnCode()) {
      return 1;
    }
    $lines = explode(PHP_EOL, trim($raw));
    $snapshot = [];
    foreach ($lines as $line) {
      $data = $this->fromJson($line, $output);
      if (isset($data->snapshots)) {
        $snapshot = $data->snapshots;
      }
      continue;
    }
    return $snapshot;
  }

}