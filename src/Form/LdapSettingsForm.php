<?php

declare(strict_types=1);

namespace Drupal\ce_ldap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Code Enigma LDAP settings for this site.
 */
final class LdapSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ce_ldap_ldap_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ce_ldap.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['domain_component_root'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain Component Root'),
      '#default_value' => $this->config('ce_ldap.settings')->get('domain_component_root'),
    ];

    $form['group_commonname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group Common Name'),
      '#default_value' => $this->config('ce_ldap.settings')->get('group_commonname'),
    ];

    $form['group_base'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group Base'),
      '#default_value' => $this->config('ce_ldap.settings')->get('group_base'),
    ];

    $form['people_base'] = [
      '#type' => 'textfield',
      '#title' => $this->t('People Base'),
      '#default_value' => $this->config('ce_ldap.settings')->get('people_base'),
    ];

    $form['disabled_users_base'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disabled Users Base'),
      '#default_value' => $this->config('ce_ldap.settings')->get('disabled_users_base'),
    ];

    $form['get_all_groups_filter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Get All Groups Filter'),
      '#default_value' => $this->config('ce_ldap.settings')->get('get_all_groups_filter'),
    ];

    $form['admin_group_filter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Admin Group Filter'),
      '#default_value' => $this->config('ce_ldap.settings')->get('admin_group_filter'),
    ];

    $form['excluded_admin_groups_for_user'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Excluded Admin Groups for User'),
      '#default_value' => $this->config('ce_ldap.settings')->get('excluded_admin_groups_for_user'),
      '#description' => $this->t('Enter excluded admin groups for user, one per line. e.g (!(cn=*Admins))'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ce_ldap.settings')
      ->set('domain_component_root', $form_state->getValue('domain_component_root'))
      ->set('group_commonname', $form_state->getValue('group_commonname'))
      ->set('group_base', $form_state->getValue('group_base'))
      ->set('people_base', $form_state->getValue('people_base'))
      ->set('disabled_users_base', $form_state->getValue('disabled_users_base'))
      ->set('get_all_groups_filter', $form_state->getValue('get_all_groups_filter'))
      ->set('admin_group_filter', $form_state->getValue('admin_group_filter'))
      ->set('excluded_admin_groups_for_user', $form_state->getValue('excluded_admin_groups_for_user'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
