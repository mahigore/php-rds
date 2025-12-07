pipeline {
    agent any
    
    stages{
        stage("Repo Clone") {
            steps{
                echo "checkout github code"
                checkout scmGit(branches: [[name: '*/main']], extensions: [], userRemoteConfigs: [[url: 'https://github.com/mahigore/php-rds.git']])
            }
        }
    }
}
