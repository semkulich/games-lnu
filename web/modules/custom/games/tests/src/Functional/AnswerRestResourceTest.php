<?php

namespace Drupal\Tests\games\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\node\Entity\Node;

/**
 * Tests the Answer REST resource.
 *
 * @group games
 */
class AnswerRestResourceTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['rest', 'serialization', 'node', 'games']; // Enable necessary modules

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro'; // Specify the default theme

  /**
   * Test the Answer REST resource.
   */
  public function testAnswerRestResource() {
    // Create a content type for answers.
    $this->drupalCreateContentType(['type' => 'answer', 'name' => 'Answer']);

    // Create a node of type 'answer' with a field for responses.
    $node = Node::create([
      'type' => 'answer',
      'title' => 'Test Answer',
      'body' => ['value' => 'This is a test answer.'],
      'field_responses' => [
        '0' => [
          'value' => 'The answer to "What is Drupal?" is a content management system.',
        ],
        '1' => [
          'value' => 'The answer to "What is a REST API?" is an architectural style.',
        ],
      ],
    ]);
    $node->save();

    // Test the GET request to the Answer REST resource.
    $this->drupalGet('/api/answers/' . $node->id());
    $this->assertSession()->statusCodeEquals(200); // Assert the response code is 200 (OK).

    // Verify the JSON response contains the answers.
    $data = $this->getSession()->getPage()->getContent();
    $responses = json_decode($data, TRUE);
    $this->assertEquals('The answer to "What is Drupal?" is a content management system.', $responses['responses'][0]); // Check the first response.
    $this->assertEquals('The answer to "What is a REST API?" is an architectural style.', $responses['responses'][1]); // Check the second response.
  }

}
