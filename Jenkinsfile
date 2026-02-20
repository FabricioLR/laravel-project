pipeline {
    agent any
    triggers {
        githubPush()
    }
    stages {
        stage('Build') {
            steps {
                echo 'Iniciando build via webhook GitHub!'
            }
        }
    }
}
