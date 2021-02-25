<?php

namespace Drupal\shield_hole;

use Drupal\Core\DependencyInjection\ContainerBuilder;

use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Alters the container to override the shield middleware.
 *
 * @package Drupal\shield_hole
 */
class ShieldHoleServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Decorate the shield module to prevent it from triggering on certain
    // routes.
    $definition = $container->getDefinition('shield.middleware');
    $definition->setClass('Drupal\shield_hole\ShieldOverride');
  }

}
