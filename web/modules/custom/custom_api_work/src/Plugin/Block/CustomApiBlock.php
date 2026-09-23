<?php

namespace Drupal\custom_api_work\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Render\Markup;

/**
 * Provides a API response.
 */
#[Block(
  id: "custom_api_block",
  admin_label: new TranslatableMarkup("Custom API Block"),
  category: new TranslatableMarkup("Custom API Block")
)]

class CustomApiBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $url = 'https://punkapi-alxiw.amvera.io/v3/beers/random';
    $response = \Drupal::httpClient()->get($url);
    $data = json_decode($response->getBody(), TRUE);

    $items = [
      'name' => $data['name'],
      'tagline' => $data['tagline'],
      'image_url' => 'https://punkapi-alxiw.amvera.io/v3/images/' . $data['image'],
    ];
    // dump($items);
    return [
      '#markup' => Markup::create(
      '<div class="brand-item">
        <h3>' . htmlspecialchars($items['name'], ENT_QUOTES, 'UTF-8') . '</h3>
        <p>' . htmlspecialchars($items['tagline'], ENT_QUOTES, 'UTF-8') . '</p>
        <img src="' . htmlspecialchars($items['image_url'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($items['name'], ENT_QUOTES, 'UTF-8') . '">
      </div>'
      ),
    ];
  }

}
