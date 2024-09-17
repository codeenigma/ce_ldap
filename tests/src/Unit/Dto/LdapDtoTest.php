<?php

namespace Drupal\Tests\ce_ldap\Unit\Dto;

use Drupal\ce_ldap\Dto\LdapDto;
use Drupal\Tests\UnitTestCase;

/**
 * Class to test the LdapDto class.
 *
 * @group ce_ldap
 */
final class LdapDtoTest extends UnitTestCase {

  /**
   * Test that the LdapDto object is created from a user array.
   *
   * @test
   */
  public function ldapDtoCreatedFromUserArray() {
    $array = [
      'givenname' => [
        'count' => 1,
        0 => 'Chris',
      ],
      0 => 'somevalue',
    ];

    $ldapDtp = LdapDto::createFromArray($array);
    $this->assertEquals($array['givenname'][0], $ldapDtp->get('givenname'));
  }

  /**
   * Test that the LdapDto object is created from a group array.
   *
   * @test
   */
  public function ldapDtoCreatedFromGroupArray() {
    $array = [
      'objectclass' => [
        'count' => 2,
        0 => 'inetOrgPerson',
        1 => 'posixAccount',
      ],
    ];

    $ldapDtp = LdapDto::createFromArray($array);
    $this->assertEquals([$array['objectclass'][0], $array['objectclass'][1]], $ldapDtp->get('objectclass'));
  }

}
