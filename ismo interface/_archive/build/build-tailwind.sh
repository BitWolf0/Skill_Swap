#!/bin/bash
# Build Tailwind CSS for ISMO-SkillSwap
# Run this after editing src/tailwind.css or tailwind.config.js
DIR="$(cd "$(dirname "$0")" && pwd)"
"$DIR/tailwindcss" \
  -i "$DIR/src/tailwind.css" \
  -o "$DIR/assets/css/tailwind.css" \
  --minify
echo "✅ Tailwind CSS built: assets/css/tailwind.css"
