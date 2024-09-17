<?php

namespace Drupal\ce_ldap\Entity;

/**
 * Representation of a YubiKey.
 */
interface YubiKeyInterface {

  /**
   * Get the YubiKey ID.
   *
   * @return string
   *   The YubiKey ID.
   */
  public function getIdentifier() : string;

}
