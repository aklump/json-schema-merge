<?php

namespace AKlump\JsonSchema\Helper;

use AKlump\JsonSchema\Exception\UnmergeableSchemaException;

/**
 * Class MergeArray
 *
 * Handles merging of array values based on specified merge rules.
 */
class MergeArray {

  /**
   * @param array $value An array value to merge per $merge_rule.
   * @param int $merge_rule One of MergeRules::*
   *
   * @return array|false|mixed|string
   * @throws \AKlump\JsonSchema\Exception\UnmergeableSchemaException
   * @see \AKlump\JsonSchema\Helper\MergeRules
   *
   */
  public function __invoke(array $value, int $merge_rule) {
    switch ($merge_rule) {
      case MergeRules::EXACT_MATCH_SCALAR:
        if (count(array_unique($value)) !== 1) {
          $value = array_unique($value);
          throw new UnmergeableSchemaException(sprintf('Cannot merge inexact values: %s', implode(', ', $value)));
        }

        return reset($value);

      case MergeRules::UNIQUE_OR_CONCATENATED_SCALAR:
        $value = array_unique($value);

        return implode(' ', $value);

      case MergeRules::UNIQUE_ARRAY_OR_SCALAR:
        $value = array_unique($value);
        if (count($value) === 1) {
          $value = reset($value);
        }

        return $value;

      case MergeRules::UNIQUE_ARRAY:
        return array_unique($value);

      default:
        throw new UnmergeableSchemaException(sprintf('Unknown merge rule: %d', $merge_rule));
    }
  }

}
