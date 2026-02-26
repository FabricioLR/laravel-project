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
                            echo "APP_NAME=Laravel" > .env
                            echo "APP_ENV=local" >> .env
                            echo "APP_KEY=" >> .env
                            echo "APP_DEBUG=true" >> .env
                            echo "APP_URL=http://127.0.0.1:8000" >> .env
                            echo "DB_CONNECTION=pgsql" >> .env
                            echo "DB_HOST=172.17.0.1" >> .env
                            echo "DB_PORT=5432" >> .env
                            echo "DB_DATABASE=test" >> .env
                            echo "DB_USERNAME=fabricio" >> .env
                            echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
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
