<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Entity;

use Drupal\ce_ldap\Dto\LdapDto;

/**
 * AbstractEntity base class for entities.
 */
abstract class AbstractEntity {

  /**
   * A LDAP DTO object.
   *
   * @var \Drupal\ce_ldap\Dto\LdapDto
   */
  protected LdapDto $dto;

  /**
   * Wrap an LDAP DTO object in an interface we can trust.
   *
   * @param \Drupal\ce_ldap\Dto\LdapDto $dto
   *   An LDAP DTO object.
   *
   * @return \Drupal\ce_ldap\Entity\AbstractEntity
   *   An instance of this class.
   */
  public static function createFromDto(LdapDto $dto) {
    return new static($dto);
  }

  /**
   * {@inheritdoc}
   */
  public function getDistinguishedName() : string {
    return $this->dto->get('dn');
  }

  /**
   * {@inheritdoc}
   */
  public function getCommonName() : string {
    return $this->dto->get('cn');
  }

  /**
   * Get off the grass, use the static constructor.
   *
   * @param \Drupal\ce_ldap\Dto\LdapDto $dto
   *   A LDAP DTO object.
   *
   * @return UserInterface
   *   An instance of this class.
   */
  final private function __construct(LdapDto $dto) {
    $this->dto = $dto;
  }

}
