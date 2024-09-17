<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Entity;

/**
 * A group.
 */
final class Group extends AbstractEntity implements GroupInterface {

  /**
   * {@inheritdoc}
   */
  public function getDescription() : string {
    return $this->dto->get('description');
  }

  /**
   * {@inheritdoc}
   */
  public function getGidNumber() : string {
    return $this->dto->get('gidnumber');
  }

  /**
   * {@inheritdoc}
   */
  public function getMemberUids(): array {
    return $this->dto->get('memberuid');
  }

}
