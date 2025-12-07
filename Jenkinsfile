pipeline {
    agent any
    
    stages{
        stage("Repo Clone") {
            steps{
                echo "checkout github code"
                checkout scmGit(branches: [[name: '*/main']], extensions: [], userRemoteConfigs: [[url: 'https://github.com/mahigore/php-rds.git']])
            }
        }
        stage("Docker Image Build") {
            steps {
                sh '''
                echo "-------- Building Docker Image --------"
                docker build -t php-app:"${App_Version}" .
                echo "-------- Image Successfully Built --------"
                '''
            }
        }
    }
}
