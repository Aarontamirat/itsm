#!/bin/bash

# Pull latest changes from current branch
echo "Pulling latest changes..."
git pull

# Add all changes
echo "Adding latest changes to the repository..."
git add .

# Prompt for commit message
read -p "Enter commit message: " commitMessage

# Commit
git commit -m "$commitMessage"

# Push
git push
