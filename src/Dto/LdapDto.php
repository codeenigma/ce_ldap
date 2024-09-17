<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Dto;

/**
 * LDAP DTO class.
 *
 * A no-frills representation of LDAP data, no spicy behaviour, just the data.
 */
final class LdapDto {

  /**
   * Creates a generic data transfer object with data received from LDAP.
   *
   * LDAP returns a array of data with mixed integer and string keys: each
   * string keyed element in the array is a property with either:
   *  - a string value
   *  - an array containing one or more values
   *  - an integer (the count of the properties)
   *
   * In order to make the data more uniform and usable upstream, this method
   * creates an object and unpacks the data over it. The LDAP property names
   * become publicly accessible on the object with either single values or
   * arrays of values.
   *
   * Examples:
   *  - givenname = ['count' => 1, 0 => 'Chris'] becomes $user->givenname
   *  = 'Chris';
   *  - objectclass = ['count' => 2, 0 => 'inetOrgPerson', 1 => 'posixAccount']
   *  becomes $user->objectclass = [0 => 'inetOrgPerson', 1 => 'posixAccount']
   *
   *  Integer keyed elements of the array are discarded, the don't contain
   *  anything useful. The count element is also discarded.
   *
   * @todo Fix function return type is not void, but function is returning void
   * here.
   *
   * @param array<mixed, mixed> $entry
   *   An array of data as returned by a single element of ldap_get_entries().
   *
   * @return \Drupal\ce_ldap\Dto\LdapDto
   *   A DTO object.
   */
  public static function createFromArray(array $entry) : self {
    unset($entry['count']);

    $dto = new self();
    array_walk($entry, function ($property, $key) use ($dto) : void {
      switch (TRUE) {
        case is_int($key):
          break;

        case is_array($property):
          if (!array_key_exists('count', $property)) {
            break;
          }

          if ($property['count'] === 1) {
            $dto->{$key} = $property[0];
            break;
          }

          unset($property['count']);
          $dto->{$key} = $property;
          break;

        default:
          $dto->{$key} = $property;
          break;
      }
    });

    return $dto;
  }

  /**
   * Get a property value (or a default fallback value).
   *
   * @param string $property
   *   The property to get.
   * @param string $default
   *   A default value to return if the property key is not present.
   *
   * @return mixed
   *   The value, if it exists (or the default).
   */
  public function get(string $property, $default = '') {
    if (property_exists($this, $property)) {
      return $this->{$property};
    }

    return $default;
  }

}
