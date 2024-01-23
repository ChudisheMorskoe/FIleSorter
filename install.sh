#!/bin/bash

if ! command -v php &> /dev/null; then
    echo "PHP is not installed. Installing PHP..."
    sudo apt-get update
    sudo apt-get install -y php
else
    echo "PHP is already installed."
fi

if php -m | grep -q "zip"; then
    echo "php-zip extension is already installed."
else
    echo "php-zip extension is not installed. Installing php-zip..."
    sudo apt-get install -y php-zip
fi

echo "Installation complete."
