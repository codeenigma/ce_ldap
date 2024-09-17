<?php

namespace Drupal\Tests\ce_ldap\Kernel\Service;

use Drupal\ce_ldap\Ldap\LdapConnection;
use Drupal\ce_ldap\Ldap\LdapServer;
use Drupal\ce_ldap\Service\LdapUserService;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Password\PasswordGeneratorInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\ce_ldap\Unit\LdapResponseDataTrait;

/**
 * LdapUserService kernel tests.
 *
 * We're not testing LDAP here, just that data received from LDAP is handled
 * correctly by the LDAP DTO and LDAP Entity classes.
 *
 * @group ce_ldap
 */
final class LdapUserServiceTest extends KernelTestBase {

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
   * A mocked messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $messengerMock;

  /**
   * The mocked password generator service.
   *
   * @var \Drupal\Core\Password\PasswordGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $passwordGeneratorMock;

  /**
   * The mocked password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $passwordMock;

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

    $this->messengerMock = $this->getMockBuilder(MessengerInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->passwordGeneratorMock = $this->getMockBuilder(PasswordGeneratorInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->passwordMock = $this->getMockBuilder(PasswordInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
  }

  /**
   * Test that some properties can be read and values are what we expect.
   *
   * @test
   */
  public function itCanGetUserByUid() {
    $this->ldapServerMock->method('read')->willReturn($this->userComplete);
    $ldapUserService = new LdapUserService($this->ldapServerMock, $this->messengerMock, $this->passwordGeneratorMock, $this->passwordMock);

    $user = $ldapUserService->getUserByUid('chris');

    $this->assertEquals('Chris Maiden', $user->getCommonName());
    $this->assertEquals('chris', $user->getUid());
    $this->assertEquals('/home/chris', $user->getHomeDirectory());
    $this->assertEquals('chris.maiden@codeenigma.com', $user->getMail());
  }

  /**
   * Test that we gracefully handle data that's missing.
   *
   * @test
   */
  public function thatMissingDataIsHandledGraceFully() {
    $this->ldapServerMock->method('read')->willReturn($this->userPartial);
    $ldapUserService = new LdapUserService($this->ldapServerMock, $this->messengerMock, $this->passwordGeneratorMock, $this->passwordMock);

    $user = $ldapUserService->getUserByUid('chris');

    $this->assertEquals('Chris Maiden', $user->getCommonName());
    $this->assertEquals('', $user->getMail());
  }

}
