pipeline {
    agent any
    triggers {
        githubPush()
    }
    stages {
         stage('Build & Test') {
            steps {
                script {
                    docker.build("laravel-test", "-f Dockerfile.test .").inside {
                        sh 'cp .env.example .env'
                        sh 'composer install'
                        sh 'php artisan key:generate'
                        sh 'touch database/database.sqlite'
                        sh 'php artisan migrate --env=testing'
                        sh 'php artisan test'
                    }
                }
                sh 'docker rmi laravel-test:latest'
            }
        }
        stage('Build Production Image') {
            steps {
                script {
                    docker.build("laravel-prod", "-f Dockerfile.prod .").inside {
                        sh 'cp .env.example .env'
                    }
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh 'docker stop laravel-prod || true'
                    sh 'docker rm laravel-prod || true'
                    sh 'docker run -d --name laravel-prod -p 8000:8000 laravel-prod:latest'
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
