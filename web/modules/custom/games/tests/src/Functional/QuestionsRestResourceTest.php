<?php

namespace Drupal\Tests\games\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Tests the Questions REST resource.
 *
 * @group games
 */
class QuestionsRestResourceTest extends BrowserTestBase {

  /**
   * The default theme for testing.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['rest', 'node', 'games', 'field', 'text', 'user', 'serialization'];


  public function testQuestionsRestResource() {
    // Create a content type for the test.
    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Page']);

    // Create a node of type 'page' with a field for questions.
    $node = Node::create([
      'type' => 'page',
      'title' => 'Test Node',
      'body' => ['value' => 'This is a test node.'],
      'field_questions' => [
        '0' => [
          'value' => 'What is Drupal?',
        ],
        '1' => [
          'value' => 'What is a REST API?',
        ],
      ],
    ]);
    $node->save();

    // Debugging: Check if the node exists
    $this->assertTrue($node->id() > 0, 'Node was created successfully.');

    // Test the GET request to the Questions REST resource.
    $this->drupalGet('/api/questions/' . $node->id());
    $this->assertSession()->statusCodeEquals(200); // Assert the response code is 200 (OK).

    // Verify the JSON response contains the questions.
    $data = $this->getSession()->getPage()->getContent();
    $questions = json_decode($data, TRUE);
    $this->assertEquals('What is Drupal?', $questions['questions'][0]); // Check the first question.
    $this->assertEquals('What is a REST API?', $questions['questions'][1]); // Check the second question.
  }


}
