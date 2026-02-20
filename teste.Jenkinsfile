pipeline {
    agent {
        docker {
            image 'node:18'      // Node.js dentro do container
            args '-u root:root'  // executa como root dentro do container
        }
    }

    environment {
        APP_NAME = 'my-express-app'
        BUILD_DIR = 'dist'
        REMOTE_USER = 'usuario'               // usuário do servidor remoto
        REMOTE_HOST = 'meu-servidor.com'      // IP ou hostname do servidor remoto
        REMOTE_PATH = '/var/www/my-express-app' // caminho de deploy no servidor
    }

    stages {

        stage('Install Dependencies') {
            steps {
                echo 'Instalando dependências no container...'
                sh 'npm ci'
            }
        }

        stage('Lint') {
            steps {
                echo 'Rodando linter no container...'
                sh 'npx eslint src/**/*.ts'
            }
        }

        stage('Test') {
            steps {
                echo 'Rodando testes no container...'
                sh 'npm test'
            }
        }

        stage('Build') {
            steps {
                echo 'Compilando TypeScript no container...'
                sh 'npx tsc'
            }
        }

        stage('Deploy') {
            agent any  // volta a usar o agente padrão do Jenkins (host)
            steps {
                echo 'Fazendo deploy da aplicação no servidor remoto...'
                sh """
                    ssh ${REMOTE_USER}@${REMOTE_HOST} '
                        set -e
                        echo "Parando aplicação existente..."
                        pm2 stop ${APP_NAME} || true
                        echo "Removendo build antigo..."
                        rm -rf ${REMOTE_PATH}
                        mkdir -p ${REMOTE_PATH}
                    '
                    echo "Copiando arquivos compilados para o servidor..."
                    scp -r package.json package-lock.json ${BUILD_DIR} src ecosystem.config.js ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/
                    ssh ${REMOTE_USER}@${REMOTE_HOST} '
                        cd ${REMOTE_PATH}
                        echo "Instalando dependências de produção..."
                        npm ci --production
                        echo "Iniciando ou reiniciando aplicação com PM2..."
                        pm2 start ecosystem.config.js --only ${APP_NAME} || pm2 restart ${APP_NAME}
                    '
                """
            }
        }
    }

    post {
        success {
            echo 'Build, teste e deploy concluídos com sucesso!'
        }
        failure {
            echo 'O build falhou!'
        }
    }
}
