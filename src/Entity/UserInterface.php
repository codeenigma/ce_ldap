<?php

namespace Drupal\ce_ldap\Entity;

use Drupal\ce_ldap\Collection\YubiKeyCollection;

/**
 * Representation of an LDAP user.
 */
interface UserInterface {

  /**
   * Common name.
   *
   * @return string
   *   The common name.
   */
  public function getCommonName() : string;

  /**
   * Home directory.
   *
   * @return string
   *   The home directory.
   */
  public function getHomeDirectory() : string;

  /**
   * Surname.
   *
   * @return string
   *   The surname.
   */
  public function getSurname() : string;

  /**
   * UID.
   *
   * @return string
   *   The UID.
   */
  public function getUid() : string;

  /**
   * UID number.
   *
   * @return string
   *   The UID number.
   */
  public function getUidNumber() : string;

  /**
   * YubiKeys.
   *
   * @return \Drupal\ce_ldap\Collection\YubiKeyCollection
   *   A collection of YubiKeys.
   */
  public function getYubiKeys() : YubiKeyCollection;

  /**
   * Display name.
   *
   * @return string
   *   The display name.
   */
  public function getDisplayName() : string;

  /**
   * Given name.
   *
   * @return string
   *   The given name.
   */
  public function getGivenName() : string;

  /**
   * Mail.
   *
   * @return string
   *   The email.
   */
  public function getMail() : string;

  /**
   * GID Number.
   *
   * @return string
   *   The GID Number.
   */
  public function getGidNumber() : string;

  /**
   * Organisation.
   *
   * @return string
   *   The organisation.
   */
  public function getOrganisation() : string;

  /**
   * Distinguished name.
   *
   * @return string
   *   The distinguished name.
   */
  public function getDistinguishedName() : string;

  /**
   * Title.
   *
   * @return string
   *   The title.
   */
  public function getTitle() : string;

}
