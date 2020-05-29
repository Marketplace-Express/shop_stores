#!/bin/bash

source ./utilities/progressbar.sh || exit 1

echo "Installing dependencies ..."

i=0
draw_progress_bar $i 10 "dependencies"
for dependency in wget libfreetype6-dev libpng-dev libjpeg-dev libcurl4-gnutls-dev libyaml-dev libicu-dev libzip-dev unzip git; do
  apt-get install -y --no-install-recommends ${dependency} > /dev/null
  i=$((i+1))
  draw_progress_bar $i 10 "dependencies"
done
echo