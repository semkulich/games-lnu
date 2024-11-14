<?php

namespace Drupal\Tests\games\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Tests the Questions REST resource.
 *
 * @group games
 */
class QuestionsRestResourceKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'field',
    'text',
    'serialization',
    'rest',
    'games',
  ];

  /**
   * A user with permission to access the REST endpoint.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Install the schema for the module under test.
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');

    $this->installConfig(['field', 'node', 'text', 'rest']);

    // Rebuild the routes.
    \Drupal::service('router.builder')->rebuild();

    // Create a content type.
    $type = NodeType::create(['type' => 'page', 'name' => 'Page']);
    $type->save();

    // Add the necessary field (field_questions) to the content type 'page'.
    \Drupal::entityTypeManager()->getStorage('field_storage_config')->create([
      'field_name' => 'field_questions',
      'entity_type' => 'node',
      'type' => 'string',
    ])->save();

    \Drupal::entityTypeManager()->getStorage('field_config')->create([
      'field_name' => 'field_questions',
      'entity_type' => 'node',
      'bundle' => 'page',
      'label' => 'Questions',
    ])->save();
  }

  /**
   * Tests the Questions REST resource.
   */
  public function testQuestionsRestResource() {
    // Create a node of type 'page' with a field for questions.
    $node = Node::create([
      'type' => 'page',
      'title' => 'Test Node',
      'field_questions' => [
        ['value' => 'Як створити компонент у Drupal?'],
      ],
    ]);
    $node->save();

    // Simulate a REST request.
    $request = Request::create('/api/questions/' . $node->id(), 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
    \Drupal::requestStack()->push($request);

    // Load the rest resource plugin.
    /** @var \Drupal\games\Plugin\rest\resource\QuestionsRestResource $resource */
    $resource = \Drupal::service('plugin.manager.rest')->createInstance('questions_rest_resource', []);

    // Invoke the get method.
    $response = $resource->get($node->id());

    // Check the response.
    $data = $response->getResponseData();

    $this->assertEquals([
      'questions' => [
        'Як створити компонент у Drupal?',
      ],
    ], $data);
  }

  /**
   * Tests the Questions REST resource with an invalid node.
   */
  public function testQuestionsRestResourceInvalidNode() {
    // Load the rest resource plugin.
    /** @var \Drupal\games\Plugin\rest\resource\QuestionsRestResource $resource */
    $resource = \Drupal::service('plugin.manager.rest')->createInstance('questions_rest_resource', []);

    // Expect exception.
    $this->expectException(NotFoundHttpException::class);

    // Simulate a REST request for an invalid node ID.
    $resource->get(9999);
  }

  /**
   * Tests the Questions REST resource with a node of incorrect type.
   */
  public function testQuestionsRestResourceInvalidNodeType() {
    // Create a node of type 'article' (which is not allowed by the resource).
    $node = Node::create([
      'type' => 'article',
      'title' => 'Test Node',
    ]);
    $node->save();

    // Load the rest resource plugin.
    /** @var \Drupal\games\Plugin\rest\resource\QuestionsRestResource $resource */
    $resource = \Drupal::service('plugin.manager.rest')->createInstance('questions_rest_resource', []);

    // Expect exception.
    $this->expectException(NotFoundHttpException::class);

    // Simulate a REST request.
    $resource->get($node->id());
  }
}
