pipeline {
    agent any

    parameters {
    string(name: "App_Version", description: "provide application version")
    }
    environment {
            // AWS Configuration
            AWS_REGION = 'ap-southeast-1'  // Your region
            AWS_ACCOUNT_ID = '864981735502'
            ECR_REGISTRY = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
            ECR_REPOSITORY = 'project/php-app'
        }
    
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
        stage("Docker Image Tag") {
            steps{
                sh """
                echo "-------- Tagging Docker Image --------"
                docker tag php-app:"${App_Version}" "${ECR_REGISTRY}"/"${ECR_REPOSITORY}":"${App_Version}"
                echo "-------- Tagging Docker Image Completed."
                """
            }
        }
        stage("Loggingin & Pushing Docker image to ECR") {
            steps {
                sh """
                echo "-------- Logging To ECR  --------"
                aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
                echo "-------- Login Successful --------"

                echo "-------- Pushing Docker Image To ECR --------"
                docker push "${ECR_REGISTRY}"/"${ECR_REPOSITORY}":"${App_Version}"
                echo "-------- Docker Image Pushed Successfully --------"
                """
            }
        }
        stage("cleanup") {
            steps {
                sh """
                   echo "-------- Cleaning Up Jenkins Machine --------"
                   docker image prune -a -f
                   echo "-------- Clean Up Successful --------"
                """
            }
       }
       stage("Deployment Acceptance") {
           steps {
               input 'Trigger Deployment'
           }
       }
       stage("Triggering Deployment") {
           steps {
                 
           }
      }
    }
}
