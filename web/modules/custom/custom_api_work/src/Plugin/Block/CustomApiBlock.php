<?php

namespace Drupal\custom_api_work\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Render\Markup;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block that displays a random beer from an external API.
 */
#[Block(
  id: "custom_api_block",
  admin_label: new TranslatableMarkup("Custom API Block"),
  category: new TranslatableMarkup("Custom API Block")
)]
class CustomApiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Constructs a CustomApiBlock object.
   *
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ClientInterface $http_client,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * Creates an instance of the block.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param array $configuration
   *   Plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   *
   * @return static
   *   A new block instance.
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
    );
  }

  /**
   * Builds the API block.
   *
   * @return array
   *   A render array containing beer information.
   */
  public function build(): array {
    $url = 'https://punkapi-alxiw.amvera.io/v3/beers/random';

    $response = $this->httpClient->get($url);
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
