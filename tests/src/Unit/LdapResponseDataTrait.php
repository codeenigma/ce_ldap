<?php

namespace Drupal\Tests\ce_ldap\Unit;

/**
 * Provide some LDAP stub data for usage in tests.
 */
trait LdapResponseDataTrait {

  /**
   * An array of LDAP user data.
   *
   * The data that's returned if we'd called ldap_read and taken the first
   * entry, (yours truly).
   *
   * @var array
   */
  protected $userComplete = [
    'objectclass' => [
      'count' => 6,
      0 => 'inetOrgPerson',
      1 => 'posixAccount',
      2 => 'shadowAccount',
      3 => 'ldapPublicKey',
      4 => 'top',
      5 => 'yubiKeyUser',
    ],
    0 => 'objectclass',
    'cn' => [
      'count' => 1,
      0 => 'Chris Maiden',
    ],
    1 => 'cn',
    'homedirectory' => [
      'count' => 1,
      0 => '/home/chris',
    ],
    2 => 'homedirectory',
    'sn' => [
      'count' => 1,
      0 => 'Maiden',
    ],
    3 => 'sn',
    'gecos' => [
      'count' => 1,
      0 => 'Chris Maiden',
    ],
    4 => 'gecos',
    'loginshell' => [
      'count' => 1,
      0 => '/bin/bash',
    ],
    5 => 'loginshell',
    'uid' => [
      'count' => 1,
      0 => 'chris',
    ],
    6 => 'uid',
    'uidnumber' => [
      'count' => 1,
      0 => '1015',
    ],
    7 => 'uidnumber',
    'yubikeyid' => [
      'count' => 1,
      0 => 'lleffjgldcdd',
    ],
    8 => 'yubikeyid',
    'displayname' => [
      'count' => 1,
      0 => 'Chris Maiden',
    ],
    9 => 'displayname',
    'givenname' => [
      'count' => 1,
      0 => 'Chris',
    ],
    10 => 'givenname',
    'mail' => [
      'count' => 1,
      0 => 'chris.maiden@codeenigma.com',
    ],
    11 => 'mail',
    'gidnumber' => [
      'count' => 1,
      0 => '6070',
    ],
    12 => 'gidnumber',
    'o' => [
      'count' => 1,
      0 => 'Code Enigma',
    ],
    13 => 'o',
    'userpassword' => [
      'count' => 1,
      0 => '',
    ],
    14 => 'userpassword',
    'count' => 15,
    'dn' => 'uid=chris,ou=People,dc=codeenigma,dc=com',
  ];

  /**
   * An array of LDAP user data that's missing some properties.
   *
   * The data that's returned if we'd called ldap_read and taken the first
   * entry, (yours truly).
   *
   * @var array
   */
  protected $userPartial = [
    'cn' => [
      'count' => 1,
      0 => 'Chris Maiden',
    ],
  ];

  /**
   * An array of LDAP group data.
   *
   * The data that's returned if we'd called ldap_read and taken the first
   * entry, yes, you better believe it, there was a group called Dirpal in the
   * LDAP test server.
   *
   * @var array
   */
  protected $groupComplete = [
    'objectclass' => [
      'count' => 2,
      0 => 'top',
      1 => 'posixGroup',
    ],
    0 => 'objectclass',
    'gidnumber' => [
      'count' => 1,
      0 => '7517',
    ],
    1 => 'gidnumber',
    'cn' => [
      'count' => 1,
      0 => 'Dirpal',
    ],
    2 => 'cn',
    'count' => 3,
    'dn' => 'cn=dirpal,ou=Groups,dc=codeenigma,dc=com',
  ];

}
