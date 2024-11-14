<?php

namespace Drupal\Tests\games\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Tests the Answer REST resource.
 *
 * @group games
 */
class AnswerRestResourceKernelTest extends KernelTestBase {

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

    // Create a content type for answers.
    $type = NodeType::create(['type' => 'answer', 'name' => 'Answer']);
    $type->save();

    // Add the necessary fields for the content type 'answer'.
    \Drupal::entityTypeManager()->getStorage('field_storage_config')->create([
      'field_name' => 'field_question',
      'entity_type' => 'node',
      'type' => 'string',
    ])->save();

    \Drupal::entityTypeManager()->getStorage('field_config')->create([
      'field_name' => 'field_question',
      'entity_type' => 'node',
      'bundle' => 'answer',
      'label' => 'Question',
    ])->save();

    \Drupal::entityTypeManager()->getStorage('field_storage_config')->create([
      'field_name' => 'field_answer',
      'entity_type' => 'node',
      'type' => 'string',
    ])->save();

    \Drupal::entityTypeManager()->getStorage('field_config')->create([
      'field_name' => 'field_answer',
      'entity_type' => 'node',
      'bundle' => 'answer',
      'label' => 'Answer',
    ])->save();
  }

  /**
   * Tests the Answer REST resource.
   */
  public function testAnswerRestResource() {
    // Create request data.
    $data = [
      'question' => 'What is Drupal?',
      'answer' => 'Drupal is a content management system.',
    ];

    // Simulate a REST POST request.
    $request = Request::create('/api/answers', 'POST', [], [], [], [], json_encode($data));
    $request->headers->set('Content-Type', 'application/json');
    \Drupal::requestStack()->push($request);

    // Load the rest resource plugin.
    /** @var \Drupal\games\Plugin\rest\resource\AnswerRestResource $resource */
    $resource = \Drupal::service('plugin.manager.rest')->createInstance('answer_rest_resource', []);

    // Invoke the post method.
    $response = $resource->post($data);

    // Check the response.
    $response_data = $response->getResponseData();
    $this->assertEquals($data['question'], $response_data->get('field_question')->value);
    $this->assertEquals($data['answer'], $response_data->get('field_answer')->value);
  }

  /**
   * Tests the Answer REST resource with missing fields.
   */
  public function testAnswerRestResourceMissingFields() {
    // Create incomplete request data.
    $data = ['question' => 'What is Drupal?'];

    // Simulate a REST POST request.
    $request = Request::create('/api/answers', 'POST', [], [], [], [], json_encode($data));
    $request->headers->set('Content-Type', 'application/json');
    \Drupal::requestStack()->push($request);

    // Load the rest resource plugin.
    /** @var \Drupal\games\Plugin\rest\resource\AnswerRestResource $resource */
    $resource = \Drupal::service('plugin.manager.rest')->createInstance('answer_rest_resource', []);

    // Expect exception.
    $this->expectException(AccessDeniedHttpException::class);

    // Invoke the post method.
    $resource->post($data);
  }
}
