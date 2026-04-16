pipeline {
    agent any
    triggers {
        githubPush()
    }
    stages {
        stage('Build & Test') {
            steps {
                sh """
                echo "APP_NAME=Laravel" > .env
                echo "APP_ENV=testing" >> .env
                echo "APP_KEY=base64:Rf/WCB2ao4gWRlPVlGX1gYC7DFOBwGky5SGsi1sCgX8=" >> .env
                echo "APP_DEBUG=true" >> .env
                echo "DB_CONNECTION=pgsql" >> .env
                echo "DB_HOST=db" >> .env
                echo "DB_PORT=5432" >> .env
                echo "DB_DATABASE=laravel_test" >> .env
                echo "DB_USERNAME=test" >> .env
                echo "DB_PASSWORD=test" >> .env
                """
                sh 'docker compose -f docker-compose.test.yml build'
                sh 'docker compose -f docker-compose.test.yml run --rm app php artisan test'
            }
        }
        stage('Build Production') {
            steps {
                sh 'docker compose -f docker-compose.prod.yml build'
            }
        }
        stage('Deploy') {
            steps {
                withCredentials([
                    string(credentialsId: 'app-key', variable: 'APP_KEY'),
                    string(credentialsId: 'db-password', variable: 'DB_PASSWORD')
                ]) {
                    sh """
                    echo "APP_NAME=Laravel" > .env
                    echo "APP_ENV=production" >> .env
                    echo "APP_KEY=${APP_KEY}" >> .env
                    echo "APP_DEBUG=false" >> .env
                    echo "APP_URL=https://server1.fabriciolr.online" >> .env
                    echo "DB_CONNECTION=pgsql" >> .env
                    echo "DB_HOST=10.0.0.110" >> .env
                    echo "DB_PORT=5432" >> .env
                    echo "DB_DATABASE=prod" >> .env
                    echo "DB_USERNAME=prod" >> .env
                    echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
                    """
                    sh 'docker compose -f docker-compose.prod.yml down'
                    sh 'docker compose -f docker-compose.prod.yml up -d'
                }
            }
        }
    }
    
    post {
        always {
            sh 'docker compose -f docker-compose.test.yml down -v || true'
            cleanWs()
        }
    }
}
