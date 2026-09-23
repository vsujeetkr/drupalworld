<?php

namespace Drupal\custom_api_work\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides API endpoints for user-related data.
 */
class UserController extends ControllerBase {

  /**
   * Returns a list of users as a JSON response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing user ID and name data.
   */
  public function getUsers() {

    $data = [
      [
        'id' => 1,
        'name' => 'John',
      ],
      [
        'id' => 2,
        'name' => 'David',
      ],
    ];

    return new JsonResponse($data);

  }

}
