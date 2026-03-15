#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
OUT_DIR="$ROOT_DIR/playground/dist"
OUT_FILE="$OUT_DIR/byline-feed-output-demo.zip"

mkdir -p "$OUT_DIR"

npx @wp-playground/cli@latest build-snapshot \
  --blueprint="$ROOT_DIR/playground/output-demo/blueprint.json" \
  --outfile="$OUT_FILE"

echo "Built snapshot: $OUT_FILE"
