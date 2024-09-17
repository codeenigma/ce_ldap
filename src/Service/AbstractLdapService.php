<?php

namespace Drupal\ce_ldap\Service;

use Drupal\ce_ldap\Ldap\LdapServerInterface;

/**
 * A base class for all service classes.
 *
 * This class provides a constructor accepting any class implementing the
 * LdapServerInterface interface.
 */
abstract class AbstractLdapService {

  /**
   * An instance of the LDAP server.
   *
   * @var \Drupal\ce_ldap\Ldap\LdapServerInterface
   */
  protected LdapServerInterface $ldapServer;

  /**
   * LdapUserService constructor.
   *
   * @param \Drupal\ce_ldap\Ldap\LdapServerInterface $ldapServer
   *   An instance of the LDAP server.
   */
  public function __construct(LdapServerInterface $ldapServer) {
    $this->ldapServer = $ldapServer;
  }

}
