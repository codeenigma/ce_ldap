<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Entity;

use Drupal\ce_ldap\Collection\YubiKeyCollection;

/**
 * A user.
 */
final class User extends AbstractEntity implements UserInterface {

  /**
   * {@inheritdoc}
   */
  public function getHomeDirectory() : string {
    return $this->dto->get('homedirectory');
  }

  /**
   * {@inheritdoc}
   */
  public function getSurname() : string {
    return $this->dto->get('sn');
  }

  /**
   * {@inheritdoc}
   */
  public function getUid() : string {
    return $this->dto->get('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function getUidNumber() : string {
    return $this->dto->get('uidnumber');
  }

  /**
   * {@inheritdoc}
   */
  public function getYubiKeys() : YubiKeyCollection {
    $yubiKeyIdentifiers = $this->dto->get('yubikeyid');

    if (!is_array($yubiKeyIdentifiers)) {
      $yubiKeyIdentifiers = [$yubiKeyIdentifiers];
    }

    return YubiKeyCollection::createFromIdentifiers($yubiKeyIdentifiers);
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayName() : string {
    return $this->dto->get('displayname');
  }

  /**
   * {@inheritdoc}
   */
  public function getGivenName() : string {
    return $this->dto->get('givenname');
  }

  /**
   * {@inheritdoc}
   */
  public function getMail() : string {
    return $this->dto->get('mail');
  }

  /**
   * {@inheritdoc}
   */
  public function getGidNumber() : string {
    return $this->dto->get('gidnumber');
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganisation() : string {
    return $this->dto->get('o');
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() : string {
    return $this->dto->get('title');
  }

  /**
   * {@inheritdoc}
   */
  public function getPhone(): string {
    return $this->dto->get('telephonenumber');
  }

  /**
   * {@inheritdoc}
   */
  public function getMobile(): string {
    return $this->dto->get('mobile');
  }

  /**
   * {@inheritdoc}
   */
  public function getSshKeys(): array {
    $sshkeys = $this->dto->get('sshpublickey');
    if (empty($sshkeys)) {
      return [];
    }
    if (!is_array($sshkeys)) {
      return [$sshkeys];
    }
    else {
      return $sshkeys;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getLoginShell(): string {
    return $this->dto->get('loginshell');
  }

}
