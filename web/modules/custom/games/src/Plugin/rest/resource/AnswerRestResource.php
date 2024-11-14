<?php

namespace Drupal\games\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;

/**
 * Provides a REST Resource for submitting answers.
 *
 * @RestResource(
 *   id = "answer_rest_resource",
 *   label = @Translation("Answer REST Resource"),
 *   uri_paths = {
 *     "create" = "/api/answers"
 *   }
 * )
 */
class AnswerRestResource extends ResourceBase {

  protected $logger;

  /**
   * Constructs a new AnswerRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('games')
    );
  }

  /**
   * Responds to POST requests.
   *
   * @param array $data
   *   The data containing the answer and other relevant information.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the created answer node.
   */
  public function post(array $data) {
    if (empty($data['question']) || empty($data['answer'])) {
      throw new AccessDeniedHttpException('Missing required fields.');
    }

    $node = Node::create([
      'type' => 'answer',
      'title' => 'Answer to question: ' . $data['question'],
      'field_question' => $data['question'],
      'field_answer' => $data['answer'],
      'status' => 1,
    ]);

    $node->save();

    $this->logger->info('Created new answer node with ID: @id', ['@id' => $node->id()]);

    return new ResourceResponse($node, 201);
  }
}
