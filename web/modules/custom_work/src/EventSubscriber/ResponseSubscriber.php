<?php

namespace Drupal\custom_work\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface {

  /**
   * Changes in header.
   */
  public function onResponse(ResponseEvent $event) {
    $response = $event->getResponse();

    // Added X-Bulk-Import in header
    $response->headers->set('X-Bulk-Import', 'Enabled');

    // Removed X-Generator in header
    if ($response->headers->has('X-Generator')) {
      $response->headers->remove('X-Generator');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

}
