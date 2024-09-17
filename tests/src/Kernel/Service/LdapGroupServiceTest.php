<?php

namespace Drupal\Tests\ce_ldap\Kernel\Service;

use Drupal\ce_ldap\Ldap\LdapConnection;
use Drupal\ce_ldap\Ldap\LdapServer;
use Drupal\ce_ldap\Service\LdapGroupService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\ce_ldap\Unit\LdapResponseDataTrait;

/**
 * LdapGroupService kernel tests.
 *
 * We're not testing LDAP here, just that data received from LDAP is handled
 * correctly by the LDAP DTO and LDAP Entity classes.
 *
 * @group ce_ldap
 */
final class LdapGroupServiceTest extends KernelTestBase {

  use LdapResponseDataTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ce_ldap'];

  /**
   * Holds the mocked LdapServer object.
   *
   * @var \Drupal\ce_ldap\Ldap\LdapServer|\PHPUnit\Framework\MockObject\MockObject
   */
  private $ldapServerMock;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $ldapConnectionMock = $this->getMockBuilder(LdapConnection::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->ldapServerMock = $this->getMockBuilder(LdapServer::class)
      ->disableOriginalConstructor()
      ->setConstructorArgs([$ldapConnectionMock])
      ->getMock();
  }

  /**
   * Test that some properties can be read and values are what we expect.
   *
   * @test
   */
  public function itCanGetGroupByCommonName() {
    $this->ldapServerMock->method('read')->willReturn($this->groupComplete);
    $ldapGroupService = new LdapGroupService($this->ldapServerMock);

    $group = $ldapGroupService->getGroupByCommonName('dirpal');

    $this->assertEquals('Dirpal', $group->getCommonName());
    $this->assertEquals('7517', $group->getGidNumber());
  }

}
