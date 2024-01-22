#!/bin/bash

script_path=$(dirname "$(readlink -f "$0")")

php -f "$script_path/FileSorter.php"

echo "php -f \"$script_path/FileSorter.php\"executed successfully" >> ~/.bashrc
