#!/bin/bash
# converting JPEG images
find /home/bitrix/www/ -type f -and \( -iname "*.jpg" -o -iname "*.jpeg" \) \
-exec bash -c '
webp_path=$(sed 's/\.[^.]*$/.webp/' <<< "$0");
if [ ! -f "$webp_path" ]; then
cwebp -quiet -q 90 -segments 1 "$0" -o "$webp_path";
fi;' {} \;

# converting PNG images
find /home/bitrix/www/ -type f -and -iname "*.png" \
-exec bash -c '
webp_path=$(sed 's/\.[^.]*$/.webp/' <<< "$0");
if [ ! -f "$webp_path" ]; then
cwebp -quiet -q 90 -segments 1 "$0" -o "$webp_path";
fi;' {} \;
