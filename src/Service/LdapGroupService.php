<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Service;

use Drupal\ce_ldap\Dto\LdapDto;
use Drupal\ce_ldap\Entity\Group;
use Drupal\ce_ldap\Ldap\LdapServerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Your friendly group service, for all things group related!
 */
class LdapGroupService extends AbstractLdapService {

  /**
   * The group base configuration item.
   *
   * @var array|mixed|null
   */
  protected $groupBase;

  /**
   * The all groups filter configuration item.
   *
   * @var array|mixed|null
   */
  protected $allGroupsFilter;

  /**
   * The admin group filter configuration item.
   *
   * @var array|mixed|null
   */
  protected $adminGroupFilter;

  /**
   * LdapGroupService constructor.
   *
   * @param \Drupal\ce_ldap\Ldap\LdapServerInterface $ldapServer
   *   The LDAP server service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(LdapServerInterface $ldapServer, ConfigFactoryInterface $config_factory) {
    parent::__construct($ldapServer);
    $config = $config_factory->get('ce_ldap.settings');
    $this->groupBase = $config->get('group_base');
    $this->allGroupsFilter = $config->get('get_all_groups_filter');
    $this->adminGroupFilter = $config->get('admin_group_filter');
  }

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
    $base = $this->groupBase;
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
    $base = $this->groupBase;
    $filter = $this->allGroupsFilter;

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get the last used GID, and adds 1.
   *
   * @return int
   *   The int to be used as the next GID.
   */
  public function getNextGid(): int {
    $base = $this->groupBase;
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
    $dn = 'cn=' . $group['cn'] . ',' . $this->groupBase;
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
    $base = $this->groupBase;
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
    $base = $this->groupBase;
    $admin_filter = $this->adminGroupFilter;
    $filter = sprintf($admin_filter, $uid);

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
    $excluded = $this->adminGroupFilter;
    $excluded = str_replace("\r\n", '', $excluded);
    $base = $this->groupBase;
    $filter = '(&' . $excluded . '(&(memberUid=' . $user . ')))';

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
    $base = $this->groupBase;
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
