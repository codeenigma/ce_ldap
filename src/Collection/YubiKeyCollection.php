<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Collection;

use Drupal\ce_ldap\Entity\YubiKey;

/**
 * What do you call a collection of YubiKeys?
 *
 * A YubiKeyCollection of course!
 *
 * @implements \Iterator<int, YubiKey>
 */
final class YubiKeyCollection implements \Iterator, \Countable {

  /**
   * Create a collection of YubiKeys.
   *
   * @param array<int, string> $yubiKeyIdentifiers
   *   An array of YubiKey identifiers.
   *
   * @return \Drupal\ce_ldap\Collection\YubiKeyCollection
   *   A YubiKey collection.
   */
  public static function createFromIdentifiers(array $yubiKeyIdentifiers) : self {
    $yubiKeys = [];
    foreach ($yubiKeyIdentifiers as $yubiKeyIdentifier) {
      $yubiKeys[] = YubiKey::createFromIdentifier($yubiKeyIdentifier);
    }

    return new self($yubiKeys);
  }

  /**
   * A collection of YubiKey objects.
   *
   * @var array<int, YubiKey> collection
   */
  private array $collection = [];

  /**
   * Internal position.
   *
   * @var int
   */
  private int $position = 0;

  /**
   * Returns a YubiKey object.
   *
   * @return \Drupal\ce_ldap\Entity\YubiKey
   *   A YubiKey object.
   */
  public function current() : YubiKey {
    return $this->collection[$this->position];
  }

  /**
   * {@inheritdoc}
   */
  public function key() : int {
    return $this->position;
  }

  /**
   * {@inheritdoc}
   */
  public function next() : void {
    ++$this->position;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() : bool {
    return isset($this->collection[$this->position]);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() : void {
    $this->position = 0;
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return count($this->collection);
  }

  /**
   * YubiKeyCollection constructor.
   *
   * @param array<int, YubiKey> $yubiKeys
   *   An array of YubiKey objects.
   */
  final private function __construct(array $yubiKeys) {
    $this->collection = $yubiKeys;
  }

}
