<?php

namespace Drupal\custom_work\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RestResource(
 *   id = "custom_node_resource",
 *   label = @Translation("Custom Node Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/node/{nid}",
 *     "create" = "/api/node"
 *   }
 * )
 */
class NodeResource extends ResourceBase {

  /**
   * Create node.
   */
  public function post(array $data) {

    $values = [
      'type' => $data['type'],
      'title' => $data['title'],
      'status' => 1,
    ];

    $node = Node::create($values);

    if ($node->hasField('field_body') && !empty($data['field_body'])) {
      $node->set('field_body', [
        'value' => $data['field_body'],
        'format' => 'basic_html',
      ]);
    }

    $node->save();

    return new ResourceResponse([
      'message' => 'Node created successfully.',
      'nid' => $node->id(),
    ], 201);
  }

  /**
   * Update node.
   */
  public function patch($nid, array $data) {

    $node = Node::load($nid);

    if (!$node) {
      return new ResourceResponse([
        'message' => 'Node not found.',
      ], 404);
    }

    if (!empty($data['title'])) {
      $node->setTitle($data['title']);
    }

    if (!empty($data['body'])) {
      $node->set('body', [
        'value' => $data['body'],
        'format' => 'basic_html',
      ]);
    }

    $node->save();

    return new ResourceResponse([
      'message' => 'Node updated successfully.',
    ], 200);
  }

  /**
   * Delete node.
   */
  public function delete($nid) {

    $node = Node::load($nid);

    if (!$node) {
      return new ResourceResponse([
        'message' => 'Node not found.',
      ], 404);
    }

    $node->delete();

    return new ResourceResponse([
      'message' => 'Node deleted successfully.',
    ], 200);
  }

}
