<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Entity;

/**
 * A YubiKey class, woo-hoo!
 */
final class YubiKey implements YubiKeyInterface {

  /**
   * The YubiKey ID.
   *
   * @var string
   */
  private string $identifier;

  /**
   * Create a YubiKey object from a string YubiKey identifier.
   *
   * @return \Drupal\ce_ldap\Entity\YubiKey
   *   A YubiKey object.
   */
  public static function createFromIdentifier(string $identifier) : self {
    return new self($identifier);
  }

  /**
   * {@inheritdoc}
   */
  public function getIdentifier() : string {
    return $this->identifier;
  }

  /**
   * Private constructor, call a static method!
   *
   * @param string $identifier
   *   A YubiKey identifier.
   */
  final private function __construct(string $identifier) {
    $this->identifier = $identifier;
  }

}
