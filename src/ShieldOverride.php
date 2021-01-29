<?php

namespace Drupal\shield_hole;

use Drupal\shield\ShieldMiddleware;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShieldOverride.
 *
 * @package Drupal\shield_hole
 */
class ShieldOverride extends ShieldMiddleware
{

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE)
  {
    // Get the current request URI.
    $currentPath = $request->getRequestUri();

    // Get the current method (e.g. GET or POST).
    $currentMethod = $request->getMethod();

    // Fetch my config.
    $config = \Drupal::config('shield_hole.settings');
    $urls = ($config->get('urls'))["url"];

    // If method is POST or GET and path is in the $urls array.
    if (($currentMethod == 'POST' || $currentMethod == 'GET') && in_array($currentPath, $urls)){
      // If we are attempting to access the service then we handle the
      // request without invoking the Shield module.
      return $this->httpKernel->handle($request, $type, $catch);
    }

    // Always handle the request using the default Shield behaviour.
    return parent::handle($request, $type, $catch);
  }

}
