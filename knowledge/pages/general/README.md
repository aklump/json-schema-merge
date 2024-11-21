<!--
id: readme
tags: ''
-->

# JSON Schema Merging

![Hero image](../../images/json_schema_merge.jpg)

This library provides a means of merging multiple JSON schemas together.  **This is not a trivial matter and this library is in early development so use at your own risk.**  New scenarios may be added as they become apparent.

`$schema` is:

```json
{
  "$schema": "http://json-schema.org/draft-06/schema#",
  "title": "JSON Schema Merge Example",
  "type": "object",
  "properties": {
    "description": {
      "type": "string"
    }
  },
  "required": [
    "description"
  ]
}

```

`$another_schema` is:

```json
{
  "$schema": "http://json-schema.org/draft-06/schema#",
  "title": "JSON Schema Merge Example",
  "type": "object",
  "additionalProperties": false,
  "properties": {
    "title": {
      "type": "string"
    }
  },
  "required": [
    "title"
  ]
}
```

The PHP code is very simple:

```php
$merged_schema = (new \AKlump\JsonSchema\MergeSchemas())($schema, $another_schema);
```

The resulting `$merged_schema` is:

```json
{
  "$schema": "http:\/\/json-schema.org\/draft-06\/schema#",
  "title": "JSON Schema Merge Example",
  "type": "object",
  "additionalProperties": false,
  "properties": {
    "title": {
      "type": "string"
    },
    "description": {
      "type": "string"
    }
  },
  "required": [
    "title",
    "description"
  ]
}
```

## Mergeability

Many schemas cannot be merged together without data loss. For example if there is a mismatch in the `$schema` value. When this type of situation occurs, an `\AKlump\JsonSchema\Exception\UnmergeableSchemaException` is thrown. For merge logic you can study the source code.

{{ composer.install|raw }}
