<?php

namespace Drupal\games\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a REST Resource for Questions.
 *
 * @RestResource(
 *   id = "questions_rest_resource",
 *   label = @Translation("Questions REST Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/questions/{id}"
 *   }
 * )
 */
class QuestionsRestResource extends ResourceBase {

  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('games')
    );
  }

  public function get($id = NULL) {
    // Спробуємо завантажити ноду за ID
    $node = Node::load($id);

    // Перевіряємо, чи нода існує та чи має правильний тип
    if (!$node || $node->getType() !== 'page') {
      throw new NotFoundHttpException();
    }

    $questions = [];

    // Перевіряємо, чи є у ноди поле з питаннями
    if ($node->hasField('field_questions') && !$node->get('field_questions')->isEmpty()) {
      foreach ($node->get('field_questions') as $field_item) {
        $questions[] = $field_item->value;
      }
    }

    return new ResourceResponse(['questions' => $questions], 200);
  }
}
