#!/usr/bin/env bash
set -euo pipefail

PLUGIN_SLUG="stampvault"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD_DIR="$ROOT_DIR/build"
DIST_DIR="$ROOT_DIR/dist"
ZIP_NAME="${PLUGIN_SLUG}.zip"

echo "==> Cleaning previous dist"
rm -rf "$DIST_DIR" "$ROOT_DIR/$ZIP_NAME"
mkdir -p "$DIST_DIR"

echo "==> Checking production PHP dependencies"
if command -v composer >/dev/null 2>&1; then
  if grep -q '"require"' "$ROOT_DIR/composer.json"; then
    echo "Installing production dependencies via Composer"
    (cd "$ROOT_DIR" && composer install --no-dev --quiet || true)
  else
    echo "No production composer requirements detected; skipping composer install"
  fi
else
  echo "Composer not found; skipping (none required)" >&2
fi

echo "==> Building block assets"
npm run build --silent

if [ ! -d "$BUILD_DIR/blocks" ]; then
  echo "Build failed: build/blocks directory missing" >&2
  exit 1
fi

echo "==> Copying plugin files"
rsync -a --exclude node_modules/ \
  --exclude src/ \
  --exclude .git/ \
  --exclude dist/ \
  --exclude scripts/ \
  --exclude docs/ \
  --exclude .gitignore \
  --exclude package.json \
  --exclude package-lock.json \
  --exclude composer.json \
  --exclude composer.lock \
  --exclude vendor/ \
  --exclude README.md \
  --exclude '.*' \
  --exclude .DS_Store \
  --exclude '*.sh' \
  "$ROOT_DIR/" "$DIST_DIR/$PLUGIN_SLUG/"

echo "==> Including built assets"
rsync -a "$BUILD_DIR" "$DIST_DIR/$PLUGIN_SLUG/"

echo "==> Creating zip"
(cd "$DIST_DIR" && zip -rq "$ZIP_NAME" "$PLUGIN_SLUG")
mv "$DIST_DIR/$ZIP_NAME" "$ROOT_DIR/"

echo "==> Done: $ROOT_DIR/$ZIP_NAME"
