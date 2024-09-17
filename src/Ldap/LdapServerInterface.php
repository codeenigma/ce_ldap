<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Ldap;

/**
 * Representation of a Ldap Server.
 */
interface LdapServerInterface {

  /**
   * LDAP link string constant.
   */
  const LDAP_LINK = 'ldap link';

  /**
   * LDAP result string constant.
   */
  const LDAP_RESULT = 'ldap result';

  /**
   * Read data from the LDAP server.
   *
   * We use read (when we know the DN) as it's more efficient than search.
   *
   * @param string $distinguishedName
   *   The distinguished name of the LDAP object to read.
   * @param string $filter
   *   The filter to apply.
   *
   * @throws \RuntimeException
   *
   * @return array<mixed, mixed>
   *   An array representation of the LDAP object.
   */
  public function read(string $distinguishedName, string $filter = '(objectclass=*)') : array;

  /**
   * List entries from the LDAP server.
   *
   * @param string $distinguishedName
   *   The distinguished name of the LDAP objects to list.
   * @param string $filter
   *   The filter to apply.
   * @param array $attributes
   *   Any additional attributes to restrict the LDAP search by.
   *
   * @throws \RuntimeException
   *
   * @return array<mixed, mixed>
   *   Entries as returned by the ldap_get_entries() function.
   */
  public function list(string $distinguishedName, string $filter = '(objectclass=*)', array $attributes = []) : array;

  /**
   * Add a new entry into the LDAP server.
   *
   * @param string $distinguishedName
   *   The distinguished name of the LDAP objects to create.
   * @param array $entry
   *   The data to create the new entry.
   */
  public function create(string $distinguishedName, array $entry) : bool;

  /**
   * Update an entry into the LDAP server.
   *
   * @param string $distinguishedName
   *   The distinguished name of the LDAP objects to update.
   * @param array $entry
   *   The data to update the entry.
   *
   * @return bool
   *   Whether the entry has been updated or not.
   */
  public function update(string $distinguishedName, array $entry): bool;

  /**
   * Remove an entry from the LDAP server.
   *
   * @param string $distinguishedName
   *   The distinguished name of the LDAP objects to update.
   *
   * @return bool
   *   Whether the entry has been deleted or not.
   */
  public function delete(string $distinguishedName): bool;

}
