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
                        withCredentials([
                            string(credentialsId: 'app-key', variable: 'APP_KEY'),
                            string(credentialsId: 'db-password', variable: 'DB_PASSWORD')
                        ]) {
                            sh '''
                                export DB_PASSWORD=${DB_PASSWORD}
                                export DB_DATABASE=test
                                export DB_USERNAME=fabricio
                                export APP_NAME=Laravel
                                export APP_ENV=local
                                export APP_KEY=${APP_KEY}
                                export APP_DEBUG=true
                                export APP_URL=http://127.0.0.1:8000
                                export DB_CONNECTION=pgsql
                                export DB_HOST=172.17.0.1
                                export DB_PORT=5432
                            '''
                            sh 'composer install'
                            sh 'npm install'
                            sh 'echo $APP_ENV && php artisan migrate --force'
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
