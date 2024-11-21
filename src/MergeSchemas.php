<?php

namespace AKlump\JsonSchema;

use AKlump\JsonSchema\Helper\MergeArray;
use AKlump\JsonSchema\Helper\MergeRules;

class MergeSchemas {

  public function __invoke(...$schemas) {
    $schema = array_shift($schemas);
    while ($merge_schema = array_shift($schemas)) {
      $schema = array_merge_recursive($schema, $merge_schema);
    }

    $this->recursivelyProcessValues($schema);

    return $schema;
  }


  private function recursivelyProcessValues(&$value, $context = NULL) {
    $context['depth'] = $context['depth'] ?? 0;
    $context['tree'] = $context['tree'] ?? [];

    if (isset($context['key'])) {
      if ($context['depth'] === 1) {
        $rules = $this->getRootMergeRules();
      }
      elseif ($context['assigning']) {
        $rules = $this->getAssignmentRules();
      }
      else {
        $rules = $this->getMergeRules();
      }
      if (is_array($value) && isset($rules[$context['key']])) {
        $value = (new MergeArray())($value, $rules[$context['key']]);
      }
    }
    if (is_array($value)) {
      foreach ($value as $k => &$v) {
        $context['assigning'] = $this->isKeyPropertyName($context);
        $context['key'] = $k;
        ++$context['depth'];
        $context['tree'][] = $k;
        $this->recursivelyProcessValues($v, $context);
        array_pop($context['tree']);
        --$context['depth'];
        $context['assigning'] = FALSE;
      }
      unset($v);
    }
  }

  private function getRootMergeRules(): array {
    return [
      '$id' => MergeRules::EXACT_MATCH_SCALAR,
      '$schema' => MergeRules::EXACT_MATCH_SCALAR,
      '$comment' => MergeRules::UNIQUE_OR_CONCATENATED_SCALAR,
      'title' => MergeRules::UNIQUE_OR_CONCATENATED_SCALAR,
      'type' => MergeRules::UNIQUE_ARRAY_OR_SCALAR,
      'description' => MergeRules::UNIQUE_OR_CONCATENATED_SCALAR,
    ];
  }

  private function getAssignmentRules() {
    return [
      'const' => MergeRules::UNIQUE_ARRAY_OR_SCALAR,
      'enum' => MergeRules::UNIQUE_ARRAY,
      'description' => MergeRules::UNIQUE_OR_CONCATENATED_SCALAR,
      'type' => MergeRules::UNIQUE_ARRAY_OR_SCALAR,
    ];
  }

  private function getMergeRules(): array {
    return [
      'type' => MergeRules::UNIQUE_ARRAY_OR_SCALAR,
    ];
  }

  private function isKeyPropertyName($context) {
    if (!isset($context['tree'][count($context['tree']) - 2])) {
      return FALSE;
    }

    return $context['tree'][count($context['tree']) - 2] === 'properties';
  }

}
