<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Ldap;

use LDAP\Connection;

/**
 * An LDAP server connection implementation.
 */
class LdapConnection {

  /**
   * LDAP link string constant.
   */
  private const LDAP_LINK = 'ldap link';

  /**
   * LDAP protocol version.
   */
  private const LDAP_PROTOCOL_VERSION = 3;

  /**
   * LDAP network timeout.
   */
  private const LDAP_NETWORK_TIMEOUT = 10;

  /**
   * Holds an LDAP link resource.
   *
   * @var \LDAP\Connection
   */
  private $connection;

  /**
   * Constructor.
   *
   * @param LdapSettings $settings
   *   A instance of LdapSettings.
   */
  public function __construct(LdapSettings $settings) {
    // ToDO Make port here configurable via $settings.
    $connection = ldap_connect($settings->getHost(), 636);

    if ($connection === FALSE) {
      throw new \RuntimeException('Could not connect to the LDAP server.');
    }

    if (!($connection instanceof Connection)) {
      throw new \RuntimeException('Unable to obtain a LDAP link resource.');
    }

    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, self::LDAP_PROTOCOL_VERSION);
    ldap_set_option($connection, LDAP_OPT_REFERRALS, FALSE);
    ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, self::LDAP_NETWORK_TIMEOUT);

    if (ldap_bind($connection, $settings->getBindDistinguishedName(), $settings->getBindPassword()) === FALSE) {
      throw new \RuntimeException('Unable to bind to LDAP.');
    }

    $this->connection = $connection;
  }

  /**
   * Share the connection.
   *
   * @return \LDAP\Connection
   *   An LDAP connection resource.
   */
  public function getConnection() {
    return $this->connection;
  }

}
