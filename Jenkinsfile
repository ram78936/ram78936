pipeline {
    agent any
    options { timestamps(); disableConcurrentBuilds() }
    environment {
        IMAGE_NAME = 'foodgo-ciap'
    }
    stages {
        stage('Checkout') {
            steps { checkout scm }
        }
        stage('PHP Syntax Validation') {
            steps {
                sh '''
                  set -eu
                  find foodgo -type f -name '*.php' -print0 | xargs -0 -n1 php -l
                '''
            }
        }
        stage('Filesystem Security Scan') {
            steps {
                sh 'trivy fs --scanners vuln,misconfig,secret --exit-code 0 --format table .'
            }
        }
        stage('Build Container Image') {
            steps { sh 'docker build -t ${IMAGE_NAME}:${BUILD_NUMBER} .' }
        }
        stage('Container Image Scan') {
            steps {
                sh 'trivy image --scanners vuln,misconfig,secret --exit-code 0 --format table ${IMAGE_NAME}:${BUILD_NUMBER}'
            }
        }
        stage('Deployment Approval') {
            steps {
                echo 'For the student demo, run docker compose up only after reviewing scan results and setting secrets.'
                echo 'Configure a protected deployment stage and credentials before using any shared/production environment.'
            }
        }
    }
    post {
        always { echo 'Save Jenkins console output and scan evidence for the CIAP report.' }
    }
}
