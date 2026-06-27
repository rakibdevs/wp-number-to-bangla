#!/usr/bin/env bash
#
# Deploy the plugin to the WordPress.org SVN repository.
#
# Prerequisites:
#   - Subversion installed (macOS: `brew install svn`)
#   - A WordPress.org account that is a committer on the plugin
#
# Usage:
#   bin/deploy-wporg.sh
#
# The script prepares a local SVN working copy and prints the exact `svn ci`
# command to run. It never commits for you — you run the final commit with your
# own credentials so nothing is published unintentionally.

set -euo pipefail

SLUG="number-to-bangla"
SVN_URL="https://plugins.svn.wordpress.org/${SLUG}"
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD="${TMPDIR:-/tmp}/wporg-${SLUG}"

VERSION="$(grep -i 'Stable tag:' "${PLUGIN_DIR}/readme.txt" | head -1 | sed -E 's/.*:[[:space:]]*//' | tr -d '[:space:]')"
HEADER_VERSION="$(grep -i 'Version:' "${PLUGIN_DIR}/number-to-bangla.php" | head -1 | sed -E 's/.*:[[:space:]]*//' | tr -d '[:space:]')"

if [[ "${VERSION}" != "${HEADER_VERSION}" ]]; then
  echo "ERROR: Stable tag (${VERSION}) != plugin header Version (${HEADER_VERSION})." >&2
  exit 1
fi

echo "Deploying ${SLUG} v${VERSION}"

rm -rf "${BUILD}"
svn checkout "${SVN_URL}" "${BUILD}"

# 1) Sync plugin source into trunk (mirrors .distignore).
rsync -a --delete \
  --exclude='.git/' --exclude='.github/' --exclude='.gitignore' --exclude='.distignore' \
  --exclude='tests/' --exclude='phpunit.xml.dist' --exclude='.phpunit.result.cache' \
  --exclude='README.md' --exclude='assets/' --exclude='bin/' \
  --exclude='node_modules/' --exclude='vendor/' --exclude='.svn/' \
  "${PLUGIN_DIR}/" "${BUILD}/trunk/"

# 2) Marketing assets live at the SVN repo root, not in trunk.
mkdir -p "${BUILD}/assets"
rsync -a --delete "${PLUGIN_DIR}/assets/" "${BUILD}/assets/"

# 3) Stage adds/removes in trunk and assets.
cd "${BUILD}"
svn add --force trunk assets >/dev/null 2>&1 || true
# Schedule deletions for files removed since the last release (portable; no xargs -r).
svn status trunk assets | awk '/^!/ {print $2}' | while IFS= read -r f; do
  [ -n "${f}" ] && svn rm --force "${f}"
done

# 4) Create the version tag from the finalized trunk.
if svn info "${SVN_URL}/tags/${VERSION}" >/dev/null 2>&1; then
  echo "WARNING: tag ${VERSION} already exists remotely; skipping tag copy." >&2
elif [[ ! -d "tags/${VERSION}" ]]; then
  svn cp trunk "tags/${VERSION}"
fi

echo
echo "===================== SVN status ====================="
svn status
echo "======================================================"
echo
echo "Review the changes above, then publish with:"
echo
echo "  cd ${BUILD}"
echo "  svn ci -m \"Release v${VERSION}\" --username YOUR_WPORG_USERNAME"
echo
echo "After it propagates, verify: https://wordpress.org/plugins/${SLUG}/"
