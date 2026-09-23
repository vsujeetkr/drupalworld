<?php

namespace Drupal\custom_api_work\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends ControllerBase {

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
