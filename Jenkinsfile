pipeline {
    agent any

    environment {
        GITHUB_REPO = 'https://github.com/nebojsatasic/task-manager.git'
        DEPLOY_DIR = '/var/www/task_manager'
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    // Clone or pull from GitHub repository
                    git url: "${GITHUB_REPO}", credentialsId: '', branch: 'main'
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    // Copy files from the workspace to the web directory, excluding .git and .gitignore
                    sh 'sudo rsync -av --delete --exclude=".git" --exclude=".gitignore" ${WORKSPACE}/ ${DEPLOY_DIR}/'

                    // Copy files containing sensitive data from the secure location to ensure that sensitive information is not exposed in the Git repository
                    sh 'sudo cp /var/secure_data/task_manager/.env ${DEPLOY_DIR}/src/.env'
                    sh 'sudo cp /var/secure_data/task_manager/database.sqlite ${DEPLOY_DIR}/src/database/database.sqlite'

                    // Navigate to the deploy directory (source folder)
                    dir("${DEPLOY_DIR}/src") {
                        sh '''
                            # Install Laravel app
                            composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

                            # Set permissions (755 for folders and 644 for files)
                            sudo find . -type d -exec chmod 755 {} +
                            sudo find . -type f -exec chmod 644 {} +

                            # Change ownership for writable dirs
                            sudo chown -R www-data:www-data storage bootstrap/cache

                            # Set write permissions for storage & cache
                            sudo chmod -R 775 storage bootstrap/cache

                            # Change ownership of the environment file and database so Jenkins can read/write them
                            sudo chown jenkins:jenkins .env database/database.sqlite

                            # Set correct file permission for SQLite DB
                            sudo chmod 664 database/database.sqlite

                            # Secure the environment file
                            sudo chmod 640 .env

                            # Change ownership for SQLite file
                            sudo chown www-data:www-data database/database.sqlite

                            # Make artisan executable
                            sudo chmod 755 artisan

                            # Run Laravel optimizations (API-specific)
                            php artisan config:cache --no-interaction --quiet
                            php artisan route:cache --no-interaction --quiet

                            # Not used in this API-only project, but included to show awareness of Blade view caching.
                            # php artisan view:cache --no-interaction --quiet
                        '''
                    }
                }
            }
        }
    }

    post {
        always {
            echo 'Deployment complete.'
        }
    }
}
