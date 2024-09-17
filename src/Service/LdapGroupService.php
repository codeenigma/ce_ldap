<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Service;

use Drupal\ce_ldap\Dto\LdapDto;
use Drupal\ce_ldap\Entity\Group;

/**
 * Your friendly group service, for all things group related!
 */
class LdapGroupService extends AbstractLdapService {

  /**
   * Get a group by its CN (common name).
   *
   * @param string $commonName
   *   The group common name.
   *
   * @return \Drupal\ce_ldap\Entity\Group
   *   The group corresponding to the common name.
   */
  public function getGroupByCommonName(string $commonName) : Group {
    $base = sprintf('cn=%s,ou=Groups,dc=codeenigma,dc=com', $commonName);
    $ldapGroup = $this->ldapServer->read($base);

    $groupDto = LdapDto::createFromArray($ldapGroup);

    return Group::createFromDto($groupDto);
  }

  /**
   * Get groups by GID (group identifier).
   *
   * @todo Fix return array, should be collection.
   *
   * @param int $groupIdentifier
   *   The gid of the group.
   *
   * @return array<int, Group>
   *   An array of groups.
   */
  public function getGroupsByGroupIdentifier(int $groupIdentifier) : array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = sprintf('(gidNumber=%s)', (string) $groupIdentifier);

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get all groups.
   *
   * @todo Fix return array, should be collection.
   *
   * @return array
   *   An array of groups.
   */
  public function getAllGroups() : array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = '(&(!(cn=*Admins))(!(cn=*Users))(!(cn=*Security))(!(cn=*Developers)))';

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get the last used GID, and adds 1.
   *
   * @return int
   *   The int to be used as the next GID.
   */
  public function getNextGid(): int {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = '(objectClass=posixGroup)';
    $gid = 0;
    $gids = $this->ldapServer->list($base, $filter, ['gidNumber']);
    if (!empty($gids)) {
      array_shift($gids);
      foreach ($gids as $group) {
        $ldapDto = LdapDto::createFromArray($group);
        if ((int) $ldapDto->get('gidnumber') > $gid) {
          $gid = (int) $ldapDto->get('gidnumber');
        }
      }
    }

    return $gid + 1;
  }

  /**
   * Create a new group.
   *
   * @param array $group
   *   The raw data of the group.
   *
   * @return bool
   *   if the group has been created.
   */
  public function createGroup(array $group): bool {
    $dn = 'cn=' . $group['cn'] . ',ou=Groups,dc=codeenigma,dc=com';
    $group['gidNumber'] = $this->getNextGid();
    $group['objectClass'] = 'posixGroup';
    return $this->ldapServer->create($dn, $group);
  }

  /**
   * Get groups by CN.
   *
   * @todo Fix return array, should be collection.
   *
   * @param string $commonName
   *   The group common name.
   *
   * @return array<int, Group>
   *   An array of groups.
   */
  public function searchGroupsByCommonName(string $commonName): array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = sprintf('(cn=%s)', $commonName);

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get "admin" groups the user is a member of.
   *
   * @todo Fix return array, should be collection.
   *
   * @param string $uid
   *   The uid of the user.
   *
   * @return array<int, Group>
   *   An array of groups.
   */
  public function getAdminGroupsByUid(string $uid): array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = sprintf('(&(memberUid=%s)(|(cn=ceAdmins)(cn=ceSecurity)(cn=ceDevelopers)))', $uid);

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get the groups for certain username.
   *
   * @param string $user
   *   The username.
   *
   * @return array
   *   The groups.
   */
  public function getGroupsForUser(string $user): array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = '(&(!(cn=*Admins))(!(cn=*Users))(!(cn=*Security))(!(cn=*Developers))(&(memberUid=' . $user . ')))';

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get the "admin" list for a group.
   *
   * @param string $groupName
   *   The cn of the group.
   *
   * @return array
   *   An array of admins of the group.
   */
  public function getAdminsOfGroup(string $groupName): array {
    $base = 'ou=Groups,dc=codeenigma,dc=com';
    $filter = '(&(cn=' . $groupName . 'Admins))';

    $list = $this->ldapServer->list($base, $filter);

    if (empty($list)) {
      return [];
    }

    $dto = LdapDto::createFromArray($list[0]);
    $group = Group::createFromDto($dto);

    return $group->getMemberUids();
  }

}
