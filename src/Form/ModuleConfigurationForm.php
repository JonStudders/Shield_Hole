<?php

namespace Drupal\shield_hole\Form;

use Drupal\Core\Form\ConfigFormBase;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shield_hole_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'shield_hole.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('shield_hole.settings');
    $urls = ($config->get('urls'))['url'];
    $url_field = $form_state->get('num_urls');
    $form['#tree'] = TRUE;
    // Initialises my fieldset.
    $form['url_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Whitelisted URLs - Format: "/endpointURL" - Paths must be encoded'),
      '#prefix' => '<div id="url-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    if (empty($url_field) && $urls === NULL) {
        $url_field = $form_state->set('num_urls', 1);
    }
    else if (empty($url_field)) {
        //set num_urls on obj to however many in DB
        $url_field = $form_state->set('num_urls', count($urls));
    }
    $url_field = $form_state->get('num_urls');
    // Create field for every url_field in obj.
    for ($i = 0; $i < $url_field; $i++) {
      $form['url_fieldset']['url'][$i] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#default_value' => $urls[$i],
      ];
    }
    $form['actions'] = [
      '#type' => 'actions',
    ];
    // Add button.
    $form['url_fieldset']['actions']['add_url'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'url-fieldset-wrapper',
      ],
    ];
    if ($url_field > 1) {
      // Remove button.
      $form['url_fieldset']['actions']['remove_url'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove one'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'url-fieldset-wrapper',
        ],
      ];
    }
    $form_state->setCached(FALSE);
    return parent::buildForm($form, $form_state);
  }

  /**
   * Adds one field then rebuilds.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $url_field = $form_state->get('num_urls');
    $add_button = $url_field + 1;
    $form_state->set('num_urls', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Callback for +1 then rebuilds.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $url_field = $form_state->get('num_urls');
    return $form['url_fieldset'];
  }

  /**
   * Callback and removes last field in index then rebuilds.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $url_field = $form_state->get('num_urls');
    if ($url_field > 1) {
      $remove_button = $url_field - 1;
      $form_state->set('num_urls', $remove_button);
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('shield_hole.settings')
      ->set('urls', $form_state->getValue('url_fieldset'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
