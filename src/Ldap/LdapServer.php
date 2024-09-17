<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Ldap;

use LDAP\Result;

/**
 * An LDAP server implementation.
 *
 * This implementation directly wraps the methods made available through the
 * ext-ldap extension... it doesn't use any intermediary libraries.
 */
class LdapServer implements LdapServerInterface {

  /**
   * Holds an LDAP link resource.
   *
   * @var \LDAP\Connection
   */
  private $connection;

  /**
   * Server constructor.
   *
   * @param LdapConnection $connection
   *   An LDAP connection.
   */
  public function __construct(LdapConnection $connection) {
    $this->connection = $connection->getConnection();
  }

  /**
   * {@inheritdoc}
   */
  public function read(string $distinguishedName, string $filter = '(objectclass=*)') : array {
    $ldapResult = ldap_read($this->connection, $distinguishedName, $filter);

    $entries = $this->getEntriesFromResult($ldapResult);

    return $entries[0];
  }

  /**
   * {@inheritdoc}
   */
  public function list(string $distinguishedName, string $filter = '(objectclass=*)', array $attributes = []) : array {
    $ldapResult = ldap_search($this->connection, $distinguishedName, $filter, $attributes);

    $entries = $this->getEntriesFromResult($ldapResult);

    return $entries;
  }

  /**
   * {@inheritdoc}
   */
  public function create(string $distinguishedName, array $entry): bool {
    return ldap_add($this->connection, $distinguishedName, $entry);
  }

  /**
   * {@inheritdoc}
   */
  public function update(string $distinguishedName, array $entry): bool {
    return ldap_mod_replace($this->connection, $distinguishedName, $entry);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(string $distinguishedName): bool {
    return ldap_delete($this->connection, $distinguishedName);
  }

  /**
   * Get the entries out of the LDAP result.
   *
   * This method ensures the entries array includes a count and that there's at
   * least one result, otherwise it returns an empty array.
   *
   * Consider this baseline result checking.
   *
   * @param mixed $ldapResult
   *   Either a LDAP result resource (hopefully), or FALSE.
   *
   * @return array<mixed, mixed>
   *   A array of LDAP data or an empty array.
   */
  private function getEntriesFromResult($ldapResult) : array {
    $emptyResult = [];

    if ($ldapResult === FALSE) {
      return $emptyResult;
    }

    if (!($ldapResult instanceof Result)) {
      return $emptyResult;
    }

    $entries = ldap_get_entries($this->connection, $ldapResult);

    if ($entries === FALSE) {
      return $emptyResult;
    }

    if (!array_key_exists('count', $entries)) {
      return $emptyResult;
    }

    if (is_array($entries['count']) === FALSE && $entries['count'] < 1) {
      return $emptyResult;
    }

    return $entries;
  }

}
