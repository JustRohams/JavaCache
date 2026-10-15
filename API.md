# API Reference

All endpoints live under `/api/` and return **JSON** unless specified otherwise.

---

## Table of Contents

- [Common Format](#common-format)
- [UUID API](#uuid-api)
- [Convert API](#convert-api)
- [Dependencies API](#dependencies-api)
- [Manifest API](#manifest-api)
- [Error Codes](#error-codes)
- [Rate Limits](#rate-limits)
- [CORS](#cors)

---

## Common Format

### Success Response

```json
{
  "ok": true,
  "action": "generate",
  "data": { }
}
```

Error Response

```json
{
  "ok": false,
  "error": "error_code",
  "msg": "optional message (debug only)"
}
```

Request Methods

· GET and POST are both accepted
· OPTIONS returns 204 for CORS preflight
· Body must be JSON when using POST with complex data

---

UUID API

Endpoint: /api/uuid.php

Actions

generate

Generate a deterministic UUID from a key + seed.

Parameters:

Name Type Required Description
action string yes generate
key string yes Any text (plugin name, pack name)
seed int no Offset (default 0)

Example:

```bash
curl "http://localhost:8000/api/uuid.php?action=generate&key=MyPlugin&seed=0"
```

Response:

```json
{
  "ok": true,
  "action": "generate",
  "uuid": "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c",
  "key": "MyPlugin",
  "seed": 0
}
```

reverse

Look up the original key from a UUID.

Parameters:

Name Type Required Description
action string yes reverse
uuid string yes A valid UUID

Response:

```json
{
  "ok": true,
  "action": "reverse",
  "uuid": "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c",
  "key": "MyPlugin"
}
```

random

Generate a standard UUID v4.

Response:

```json
{
  "ok": true,
  "action": "random",
  "uuid": "d3b07384-d9a1-4c1e-9f0b-2c8e7a6f5d4c"
}
```

pair

Generate a Header + Module UUID pair (for Bedrock manifests).

Parameters:

Name Type Required Description
action string yes pair
key string yes Pack name

Response:

```json
{
  "ok": true,
  "action": "pair",
  "key": "My Addon",
  "header": "7e199f57-fff9-48c9-aba5-fd0835adbe7f",
  "module": "f3056213-4d18-495a-8ce9-a00a82f626d0"
}
```

---

Convert API

Endpoint: /api/convert.php

Convert coordinates between Overworld and Nether.

Parameters

Name Type Required Description
action string yes o2n (Overworld→Nether) or n2o (Nether→Overworld)
x float yes X coordinate
y float yes Y coordinate
z float yes Z coordinate

Rules

Direction X Y Z
Overworld → Nether x / 8 unchanged z / 8
Nether → Overworld x * 8 unchanged z * 8

Example

```bash
curl "http://localhost:8000/api/convert.php?action=o2n&x=800&y=64&z=1600"
```

Response:

```json
{
  "ok": true,
  "action": "o2n",
  "label": "Overworld → Nether",
  "input":  { "x": 800, "y": 64, "z": 1600 },
  "output": { "x": 100, "y": 64, "z": 200 }
}
```

---

Dependencies API

Endpoint: /api/dependencies.php

Actions

list

Return the default list of common dependencies.

Response:

```json
{
  "ok": true,
  "action": "list",
  "data": {
    "depends": [
      { "name": "FormAPI", "author": "jojoe77777", "type": "virion" }
    ],
    "softdepends": [
      { "name": "EconomyAPI", "author": "onebone", "type": "plugin" }
    ]
  }
}
```

validate

Validate a dependency structure.

Parameters:

Name Type Required Description
action string yes validate
data JSON yes { depends: [], softdepends: [] }

Response:

```json
{
  "ok": true,
  "errors": []
}
```

save

Persist a dependency configuration to data/dependencies.json.

Parameters:

Name Type Required Description
action string yes save
data JSON yes { depends: [], softdepends: [] }

Response:

```json
{ "ok": true, "action": "save" }
```

---

Manifest API

Endpoint: /api/manifest.php

Actions

generate

Build a valid Bedrock manifest.json.

Parameters (JSON body or form-encoded):

Name Type Default Description
name string My Addon Pack name
description string Created with PocketMine Tools Pack description
type string data data \| resources \| script
version string 1.0.0 Dot-separated version
min_engine string 1.20.0 Minimum engine version
stable_uuid bool false Use deterministic UUIDs

Response:

```json
{
  "ok": true,
  "action": "generate",
  "manifest": {
    "format_version": 2,
    "header": {
      "name": "My Addon",
      "description": "Created with PocketMine Tools",
      "uuid": "7e199f57-fff9-48c9-aba5-fd0835adbe7f",
      "version": [1, 0, 0],
      "min_engine_version": [1, 20, 0]
    },
    "modules": [
      {
        "type": "data",
        "uuid": "f3056213-4d18-495a-8ce9-a00a82f626d0",
        "version": [1, 0, 0]
      }
    ]
  },
  "json": "...pretty-printed JSON string..."
}
```

pack

Return a downloadable .mcpack file (ZIP containing manifest.json).

Requires: ext-zip

Parameters (JSON body):

Name Type Required Description
manifest string yes JSON string of a valid manifest

Response: Binary file — Content-Type: application/octet-stream

Example:

```bash
curl -X POST "http://localhost:8000/api/manifest.php?action=pack" \
  -H "Content-Type: application/json" \
  -d '{"manifest":"{\"format_version\":2,\"header\":{...}}"}' \
  -o pack.mcpack
```

---

Error Codes

Code HTTP Meaning
key_required 400 Missing key parameter
key_too_long 400 Key exceeds 256 characters
invalid_uuid 400 UUID format is invalid
unknown_action 400 Action parameter not recognized
invalid_action 400 Wrong action for endpoint
invalid_json 400 Body is not valid JSON
missing_field:X 400 Required field X missing
invalid_manifest 400 Manifest JSON is not valid
zip_not_available 500 ext-zip not installed
zip_create_failed 500 Could not create ZIP file
server_error 500 Unexpected server error

---

Rate Limits

Rate limiting is available but disabled by default. Enable in config/app.php:

```php
'security' => [
    'rate_limit'     => true,
    'rate_limit_max' => 60,   // requests per minute
],
```

When enabled, requests over the limit return:

```json
{ "ok": false, "error": "rate_limited" }
```

HTTP Status: 429

---

CORS

All endpoints send:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, OPTIONS
```

Preflight requests (OPTIONS) return 204 No Content.

---

Notes

· All responses use Content-Type: application/json; charset=utf-8
· JSON is pretty-printed (JSON_PRETTY_PRINT)
· Unicode is not escaped (JSON_UNESCAPED_UNICODE)
· Slashes are not escaped (JSON_UNESCAPED_SLASHES)
· Debug mode adds msg to error responses; production hides it

---

See also: README.md · INSTALL.md · CONTRIBUTING.md

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
