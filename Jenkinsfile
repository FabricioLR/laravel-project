pipeline {
    agent any
    triggers {
        githubPush()
    }
    stages {
        stage('Build & Test') {
            steps {
                script {
                    withCredentials([
                        string(credentialsId: 'app-key', variable: 'APP_KEY'),
                        string(credentialsId: 'db-password', variable: 'DB_PASSWORD')
                    ]) {
                        docker.build("laravel-test", "-f Dockerfile.test .").inside {
                            sh """
                            cat > .env <<EOL
                            APP_NAME=Laravel
                            APP_ENV=local
                            APP_KEY=
                            APP_DEBUG=true
                            APP_URL=http://127.0.0.1:8000
                            DB_CONNECTION=pgsql
                            DB_HOST=172.17.0.1
                            DB_PORT=5432
                            DB_DATABASE=test
                            DB_USERNAME=fabricio
                            DB_PASSWORD=${DB_PASSWORD}
                            EOL
                            """
                            sh 'composer install'
                            sh 'npm install'
                            sh 'npm run build'
                            sh 'php artisan key:generate'
                            sh 'php artisan migrate'
                            sh 'php artisan test'
                        }
                    }
                }
                sh 'docker rmi laravel-test:latest'
            }
        }
        stage('Build Production Image') {
            steps {
                script {
                    docker.build("laravel-prod", "-f Dockerfile.prod .")
                }
            }
        }
        stage('Deploy') {
            steps {
                script {
                    withCredentials([
                        string(credentialsId: 'app-key', variable: 'APP_KEY'),
                        string(credentialsId: 'db-password', variable: 'DB_PASSWORD')
                    ]) {
                        sh 'docker stop laravel-prod || true'
                        sh 'docker rm laravel-prod || true'
                        sh 'docker run -d --name laravel-prod -p 8000:8000 -e DB_PORT=5432 -e APP_URL=http://127.0.0.1:8000 -e DB_HOST=172.17.0.1 -e DB_CONNECTION=pgsql -e DB_PASSWORD=${DB_PASSWORD} -e DB_DATABASE=projects -e DB_USERNAME=fabricio -e APP_NAME=Laravel -e APP_ENV=production -e APP_KEY=${APP_KEY} -e APP_DEBUG=false laravel-prod:latest'
                    }
                }
            }
        }
    }
    
    post {
        always {
            cleanWs()
        }
    }
}
