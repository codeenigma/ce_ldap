<?php

namespace Drupal\Tests\ce_ldap\Unit\Ldap;

use Drupal\ce_ldap\Ldap\LdapSettings;
use Drupal\Core\Site\Settings;
use Drupal\Tests\UnitTestCase;

/**
 * Class to test the LdapSettings class.
 *
 * @group ce_ldap
 */
final class LdapSettingsTest extends UnitTestCase {

  private const HOST = 'ldaps://example.com';
  private const DN = 'cn=admin,dc=example,dc=com';
  private const PASSWORD = 'not4re4lpa55w0rd';

  /**
   * The happy settings creation path.
   *
   * @test
   */
  public function settingsCreatedSuccessfully() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => self::HOST,
          'dn' => self::DN,
          'password' => self::PASSWORD,
        ],
    ]);

    $ldapSettings = new LdapSettings($settings);

    $this->assertEquals(self::HOST, $ldapSettings->getHost());
    $this->assertEquals(self::DN, $ldapSettings->getBindDistinguishedName());
    $this->assertEquals(self::PASSWORD, $ldapSettings->getBindPassword());
  }

  /**
   * Attempt to create LDAP settings with no settings throws exception.
   *
   * @test
   */
  public function noSettingsThrowsException() {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP settings not found.');

    new LdapSettings(new Settings([]));
  }

  /**
   * Attempt to create settings with missing host throws exception.
   *
   * @test
   */
  public function settingsWithoutHostThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'dn' => self::DN,
          'password' => self::PASSWORD,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP settings must include the LDAP host, keyed: host.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with empty host throws exception.
   *
   * @test
   */
  public function settingsWithEmptyHostThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => '',
          'dn' => self::DN,
          'password' => self::PASSWORD,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP host setting cannot be empty.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with malformed host throws exception.
   *
   * @test
   */
  public function hostSettingIsMalformedThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => 'example.com',
          'dn' => self::DN,
          'password' => self::PASSWORD,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP host setting is invalid.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with missing dn throws exception.
   *
   * @test
   */
  public function settingsWithoutDnThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => self::HOST,
          'password' => self::PASSWORD,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP settings must include a bind distinguished name, keyed: dn.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with empty dn throws exception.
   *
   * @test
   */
  public function settingsWithEmptyDnThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => self::HOST,
          'dn' => '',
          'password' => self::PASSWORD,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP distinguished name setting cannot be empty.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with missing password throws exception.
   *
   * @test
   */
  public function settingsWithoutPasswordThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => self::HOST,
          'dn' => self::DN,
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP settings must include a bind password, keyed: password.');

    new LdapSettings($settings);
  }

  /**
   * Attempt to create settings with empty password throws exception.
   *
   * @test
   */
  public function settingsWithEmptyPasswordThrowsException() {
    $settings = new Settings([
      'ldap' =>
        [
          'host' => self::HOST,
          'dn' => self::DN,
          'password' => '',
        ],
    ]);

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('LDAP bind password setting cannot be empty.');

    new LdapSettings($settings);
  }

}
