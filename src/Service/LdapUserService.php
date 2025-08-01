<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Service;

use Drupal\ce_ldap\Dto\LdapDto;
use Drupal\ce_ldap\Entity\User;
use Drupal\ce_ldap\Ldap\LdapServerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Password\PasswordGeneratorInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Your friendly user service, for all things user!
 */
class LdapUserService extends AbstractLdapService {

  use StringTranslationTrait;

  /**
   * An instance of Drupal messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * An instance of Drupal password generator service.
   *
   * @var \Drupal\Core\Password\PasswordGeneratorInterface
   */
  protected PasswordGeneratorInterface $passwordGenerator;

  /**
   * An instance of Drupal password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected PasswordInterface $password;

  /**
   * The domain component root configuration item.
   *
   * @var array|mixed|null
   */
  protected $domainComponentRoot;

  /**
   * The people base configuration item.
   *
   * @var array|mixed|null
   */
  protected $peopleBase;

  /**
   * The disabled users base configuration item.
   *
   * @var array|mixed|null
   */
  protected $disabledUsersBase;

  /**
   * LdapUserService constructor.
   *
   * @param \Drupal\ce_ldap\Ldap\LdapServerInterface $ldapServer
   *   An instance of the LDAP server.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   An instance of Drupal messenger service.
   * @param \Drupal\Core\Password\PasswordGeneratorInterface $passwordGenerator
   *   An instance of Drupal password generator service.
   * @param \Drupal\Core\Password\PasswordInterface $password
   *   An instance of Drupal password service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  final public function __construct(
    LdapServerInterface $ldapServer,
    MessengerInterface $messenger,
    PasswordGeneratorInterface $passwordGenerator,
    PasswordInterface $password,
    ConfigFactoryInterface $config_factory,
  ) {
    // @todo Don't export config variables in the constructor.
    $config = $config_factory->get('ce_ldap.settings');
    $this->domainComponentRoot = $config->get('domain_component_root');
    $this->peopleBase = $config->get('people_base');
    $this->disabledUsersBase = $config->get('disabled_users_base');
    $this->ldapServer = $ldapServer;
    $this->messenger = $messenger;
    $this->passwordGenerator = $passwordGenerator;
    $this->password = $password;
  }

  /**
   * Get a user from LDAP by UID.
   *
   * @param string $uid
   *   The UID of the user we're after.
   *
   * @return \Drupal\ce_ldap\Entity\User
   *   The user object.
   */
  public function getUserByUid(string $uid) : User {
    $base = sprintf('uid=%s,' . $this->peopleBase, $uid);
    $ldapUser = $this->ldapServer->read($base);

    $userDto = LdapDto::createFromArray($ldapUser);

    return User::createFromDto($userDto);
  }

  /**
   * Get a user from LDAP by numeric UID.
   *
   * @param int $uidnumber
   *   The UID of the user we're after.
   * @param bool $raw
   *   A boolean to return raw output.
   *
   * @return \Drupal\ce_ldap\Entity\User
   *   The user object.
   */
  public function getUserByNumericUid(int $uidnumber, bool $raw = FALSE) {
    $base = $this->peopleBase;
    $filter = '(&(objectClass=*)(uidNumber=' . $uidnumber . '))';
    $ldapSearch = $this->ldapServer->list($base, $filter);
    if (!empty($ldapSearch)) {
      // Remove 'count'.
      array_shift($ldapSearch);
      $ldapUser = array_shift($ldapSearch);
      // @todo Don't do this, have another function to return a different thing.
      if ($raw) {
        return $ldapUser;
      }
      $userDto = LdapDto::createFromArray($ldapUser);
      return User::createFromDto($userDto);
    }
    return NULL;
  }

  /**
   * Get a user from LDAP by e-mail adress.
   *
   * @param string $mail
   *   The e-mail of the user we're after.
   *
   * @return \Drupal\ce_ldap\Entity\User
   *   The user object.
   */
  public function getUserByMail(string $mail) {
    $base = $this->peopleBase;
    $filter = '(&(objectClass=*)(mail=' . $mail . '))';
    $ldapSearch = $this->ldapServer->list($base, $filter);
    if (!empty($ldapSearch)) {
      // Remove 'count'.
      array_shift($ldapSearch);
      $ldapUser = array_shift($ldapSearch);
      $userDto = LdapDto::createFromArray($ldapUser);
      return User::createFromDto($userDto);
    }
    return NULL;
  }

  /**
   * Get users from LDAP by Group Id.
   *
   * @param string $gid
   *   The gid of the users we're after.
   *
   * @return array
   *   The users corresponding to the search parameter.
   */
  public function getUsersfromGroupId(string $gid) : array {
    $base = $this->peopleBase;
    $filter = '(&(objectClass=*)(gidNumber=' . $gid . '))';

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get disabled users from LDAP by Group Id.
   *
   * @param string $gid
   *   The gid of the users we're after.
   *
   * @return array
   *   The users corresponding to the search parameter.
   */
  public function getDisabledUsersfromGroupId(string $gid): array {
    $base = $this->disabledUsersBase;
    $filter = '(&(objectClass=*)(gidNumber=' . $gid . '))';

    return $this->ldapServer->list($base, $filter);
  }

  /**
   * Get the last used UID (number), and adds 1.
   *
   * @return int
   *   The int to be used as the next uidNumber.
   */
  public function getNextUid(): int {
    $base = $this->domainComponentRoot;
    $filter = '(objectClass=posixAccount)';
    $uid = 0;
    $uids = $this->ldapServer->list($base, $filter, ['uidNumber']);
    if (!empty($uids)) {
      array_shift($uids);
      foreach ($uids as $user) {
        $ldapDto = LdapDto::createFromArray($user);
        if ((int) $ldapDto->get('uidnumber') > $uid) {
          $uid = (int) $ldapDto->get('uidnumber');
        }
      }
    }

    return $uid + 1;
  }

  /**
   * Create a new user.
   *
   * @param array $user
   *   An array containing the user data.
   *
   * @return bool
   *   if the user has been created.
   */
  public function createUser(array $user) {
    $dn = 'uid=' . $user['uid'] . ',' . $this->peopleBase;
    $user['uidNumber'] = $this->getNextUid();
    $user['objectClass'][] = 'inetOrgPerson';
    $user['objectClass'][] = 'posixAccount';
    $user['homeDirectory'] = '/home/' . $user['uid'];
    return $this->ldapServer->create($dn, $user);
  }

  /**
   * Updates a user.
   *
   * @param string $dn
   *   The LDAP user distinguished name.
   * @param array $attribute
   *   An array containing the name and value of the attribute to update.
   *
   * @return bool
   *   if the user has been succesfully updated.
   */
  public function updateUser($dn, $attribute) {
    return $this->ldapServer->update($dn, $attribute);
  }

  /**
   * Disable the user.
   *
   * This method:
   *  - moves the user to ou=DisabledUsers
   *  - sets their password to random rubbish
   *  - creates a shadowExpire value set to 1
   *  - deletes any SSH keys
   *  - Changes GID of the user to that of the parent client group (if the
   *    user belongs to a subgroup one).
   *
   * This method, on purpose does NOT:
   *  - hobble the user uid or email address in any way. Not needed to keep
   *    users from resetting their password. LDAP won't return them if they're
   *    in the DisabledUsers group. and even if it did, that logic should be
   *    covered in the password reset validation.
   *  - Last time the 'email' change was discussed was for
   *    https://redmine.codeenigma.net/issues/18986.
   *    Outcome was it would be kept as is, since it might be a problem to
   *    re-enable a user if in the meantime there has been another account
   *    created with same email.
   *
   * @param int $uidNumber
   *   The UID number of the user.
   */
  public function disableUser(int $uidNumber):void {
    // We need raw data to prepare the creation of the disabled entry.
    $user = $this->getUserByNumericUid($uidNumber, TRUE);
    if ($user === NULL) {
      $this->messenger->addWarning($this->t('This user does not exist any more.'));
      return;
    }
    // Create a new disabled user.
    $disabledUser = [];
    foreach ($user as $key => $value) {
      if (is_numeric($key) || $key === 'count' || $key === 'dn') {
        // Don't do anything with these values.
      }
      else {
        // We need to remove the "count" entry, and otherwise iterate
        // But we need to change the structure as well.
        // Example : 'cn' => ['count' => 1, '0' => '$value'].
        if (is_array($value) && $value['count'] == 1) {
          $disabledUser[$key] = $value[0];
        }
        else {
          foreach ($value as $internal_key => $internal_value) {
            if ($internal_key !== 'count') {
              $disabledUser[$key][] = $internal_value;
            }
          }
        }
      }
    }
    // Disable the user.
    $oldDn = $user['dn'];
    unset($disabledUser['sshpublickey']);
    $disabledUser['shadowExpire'] = 1;
    $random = $this->passwordGenerator->generate(16);
    $disabledUser['userpassword'] = $this->password->hash($random);
    if (!empty($disabledUser['loginShell'])) {
      $disabledUser['loginShell'] = '/bin/false';
    }
    if (!array_search('shadowAccount', $disabledUser['objectclass'])) {
      $disabledUser['objectclass'][] = 'shadowAccount';
    }
    $disabledDn = 'uid=' . $disabledUser['uid'] . ',' . $this->disabledUsersBase;
    if (!$this->ldapServer->create($disabledDn, $disabledUser)) {
      $this->messenger->addWarning($this->t('Disabled user could not be created'));
    }
    else {
      $this->ldapServer->delete($oldDn);
      $this->messenger->addStatus($this->t('The user has been disabled'));
    }
  }

}
