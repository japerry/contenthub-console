<?php

namespace Acquia\Console\ContentHub\Client;

/**
 * Interface ContentHubServiceInterface
 *
 * @package Acquia\Console\ContentHub\Client
 */
interface ContentHubServiceInterface {

  /**
   * Returns the version of the Content Hub client library.
   *
   * @return int
   *   The version number.
   */
  public function getVersion(): int;

  /**
   * Returns a new instance of ContentHubServiceInterface.
   *
   * @return static
   *   The ContentHubService instance.
   */
  public static function new(): self;

  /**
   * Registers a new client into Content Hub.
   *
   * After registration the new client is going to be used.
   *
   * @param string $name
   *   The name of the new client.
   * @param string $api_key
   *   The Content Hub api key.
   * @param string $secret_key
   *   The Content Hub secret key.
   * @param string $hostname
   *   The Content Hub hostname.
   */
  public function register(string $name, string $api_key, string $secret_key, string $hostname);

  /**
   * Gathers Acquia ContentHub subscription clients.
   *
   * @return array
   *   Client information.
   */
  public function getClients(): array;

  /**
   * Returns webhook information from service.
   *
   * @return array
   *   Webhook information.
   */
  public function getWebhooks(): array;

  /**
   * Returns the remote settings.
   *
   * @return \Acquia\Console\ContentHub\Client\Settings
   *   The Content Hub client settings.
   */
  public function getSettings(): Settings;

  /**
   * Checks if client successfully registered.
   *
   * @return bool
   *   True if client registered.
   */
  public function checkClient(): bool;

  /**
   * Purges Content Hub subscription.
   *
   * @return mixed
   *   The response of the purge operation.
   */
  public function purge();

}
