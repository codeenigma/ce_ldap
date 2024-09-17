<?php

namespace Drupal\ce_ldap\Entity;

/**
 * Interface for Group entity objects.
 */
interface GroupInterface {

  /**
   * Description.
   *
   * @return string
   *   The description.
   */
  public function getDescription() : string;

  /**
   * GID Number.
   *
   * @return string
   *   The GID Number.
   */
  public function getGidNumber() : string;

  /**
   * Common name.
   *
   * @return string
   *   The common name.
   */
  public function getCommonName() : string;

  /**
   * Distinguished name.
   *
   * @return string
   *   The distinguished name.
   */
  public function getDistinguishedName() : string;

}
