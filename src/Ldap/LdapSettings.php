<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Ldap;

use Drupal\Core\Site\Settings;

/**
 * A class for exracting LDAP settings from site settings.
 */
final class LdapSettings {

  /**
   * The LDAP server host name.
   *
   * @var string
   */
  private string $host;

  /**
   * The LDAP server port.
   *
   * @var int The LDAP server port.
   */
  private int $port;

  /**
   * The distinguished name for the the bind.
   *
   * @var string
   */
  private string $bindDistinguishedName;

  /**
   * The bind password.
   *
   * @var string
   */
  private string $bindPassword;

  /**
   * LDAP settings extractor constructor.
   *
   * @param \Drupal\Core\Site\Settings $settings
   *   Settings should include an array, keyed as follows:
   *   - $settings['ldap']['host'] = 'host';
   *   - $settings['ldap']['port'] = 636;
   *   - $settings['ldap']['dn'] = 'dn';
   *   - $settings['ldap']['password'] = 'password';.
   *
   * @throws \RuntimeException
   */
  public function __construct(Settings $settings) {
    $ldapSettings = $settings->get('ldap', []);

    if ($ldapSettings === []) {
      throw new \RuntimeException('LDAP settings not found.');
    }

    $this->host = self::extractHostSetting($ldapSettings);
    $this->port = self::extractPortSetting($ldapSettings);
    $this->bindDistinguishedName = self::extractBindDistinguishedNameSetting($ldapSettings);
    $this->bindPassword = self::extractBindPasswordSetting($ldapSettings);
  }

  /**
   * Share the host setting.
   *
   * @return string
   *   The host.
   */
  public function getHost() : string {
    return $this->host;
  }

  public function getldapuri() : string {
    return $this->host . ':' . $this->port;
  }

  /**
   * Share the bind distinguished name setting.
   *
   * @return string
   *   The bind distinguished name setting.
   */
  public function getBindDistinguishedName() : string {
    return $this->bindDistinguishedName;
  }

  /**
   * Share the bind password setting.
   *
   * @return string
   *   The bind password.
   */
  public function getBindPassword() : string {
    return $this->bindPassword;
  }

  /**
   * Extract the host setting from the LDAP settings array.
   *
   * @param array<string, string> $ldapSettings
   *   An array of LDAP settings.
   *
   * @return string
   *   The host setting.
   */
  private static function extractHostSetting(array $ldapSettings) : string {
    if (!array_key_exists('host', $ldapSettings)) {
      throw new \RuntimeException('LDAP settings must include the LDAP host, keyed: host.');
    }

    /** @var string $host */
    $host = trim($ldapSettings['host']);

    if (mb_strlen($host) === 0) {
      throw new \RuntimeException('LDAP host setting cannot be empty.');
    }

    if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
      throw new \RuntimeException('LDAP host setting is invalid.');
    }

    return $host;
  }

  private static function extractPortSetting(array $ldapSettings) : int {
    if (!array_key_exists('port', $ldapSettings)) {
      throw new \RuntimeException('LDAP settings must include the LDAP port, keyed: port.');
    }

    /** @var int $port */
    $port = (int) $ldapSettings['port'];

    if ($port <= 0 || $port > 65535) {
      throw new \RuntimeException('LDAP port setting must be a valid port number.');
    }

    return $port;
  }

  /**
   * Extract the bind distinguished name setting from the LDAP settings array.
   *
   * @param array<string, string> $ldapSettings
   *   An array of LDAP settings.
   *
   * @return string
   *   The bind distinguished name setting.
   */
  private static function extractBindDistinguishedNameSetting(array $ldapSettings) : string {
    if (!array_key_exists('dn', $ldapSettings)) {
      throw new \RuntimeException('LDAP settings must include a bind distinguished name, keyed: dn.');
    }

    /** @var string $bindDistinguishedName */
    $bindDistinguishedName = trim($ldapSettings['dn']);

    if ($bindDistinguishedName === '') {
      throw new \RuntimeException('LDAP distinguished name setting cannot be empty.');
    }

    return $bindDistinguishedName;
  }

  /**
   * Extract the bind password setting from the LDAP settings array.
   *
   * @param array<string, string> $ldapSettings
   *   An array of LDAP settings.
   *
   * @return string
   *   The bind password setting.
   */
  private static function extractBindPasswordSetting(array $ldapSettings) : string {
    if (!array_key_exists('password', $ldapSettings)) {
      throw new \RuntimeException('LDAP settings must include a bind password, keyed: password.');
    }

    /** @var string $bindPassword */
    $bindPassword = trim($ldapSettings['password']);

    if ($bindPassword === '') {
      throw new \RuntimeException('LDAP bind password setting cannot be empty.');
    }

    return $bindPassword;
  }

}
