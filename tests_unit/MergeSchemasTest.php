<?php

namespace AKlump\JsonSchema\Tests\Unit;

use AKlump\JsonSchema\Exception\UnmergeableSchemaException;
use AKlump\JsonSchema\Tests\Unit\TestingTraits\TestWithFilesTrait;
use AKlump\JsonSchema\MergeSchemas;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\JsonSchema\MergeSchemas
 * @uses   \AKlump\JsonSchema\Helper\MergeArray
 */
class MergeSchemasTest extends TestCase {

  use TestWithFilesTrait;

  public function testUnmergeableExceptionIsThrown() {
    $a = ['$schema' => 'http://json-schema.org/draft-06/schema#'];
    $b = ['$schema' => 'http://json-schema.org/draft-07/schema#'];
    $this->expectException(UnmergeableSchemaException::class);
    (new MergeSchemas())($a, $b);
  }

  public static function dataFortestInvokeProvider(): array {
    $tests = [];
    $tests[] = [
      ['readme1.schema.json', 'readme2.schema.json'],
      'readme1--readme2.schema.json',
    ];
    $tests[] = [
      ['alpha.schema.json', 'bravo.schema.json'],
      'alpha--bravo.schema.json',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke(array $paths_to_merge, string $expected_result_path) {
    $schemas = $this->loadJsonSchemaTestFilepaths(... $paths_to_merge);
    $result = (new MergeSchemas())(...$schemas);
    $this->assertSameAsMergedTestFilepath($expected_result_path, $result);
  }

}
