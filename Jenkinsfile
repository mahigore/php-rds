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

            // EC2 Configuration
            EC2_HOST = '54.169.149.223'  // Application EC2 (2nd EC2)
            EC2_USER = 'ec2-user'

            // Database Configuration (store only non-sensitive info here)
            DB_HOST = 'php-app-database.cby2mqygynq9.ap-southeast-1.rds.amazonaws.com'
            DB_NAME = 'php-app-database'
            DB_USER = 'admin'
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
                sh '''
                echo "-------- Tagging Docker Image --------"
                docker tag php-app:"${App_Version}" "${ECR_REGISTRY}"/"${ECR_REPOSITORY}":"${App_Version}"
                echo "-------- Tagging Docker Image Completed."
                '''
            }
        }
        stage("Loggingin & Pushing Docker image to ECR") {
            steps {
                sh '''
                echo "-------- Logging To ECR  --------"
                aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
                echo "-------- Login Successful --------"

                echo "-------- Pushing Docker Image To ECR --------"
                docker push "${ECR_REGISTRY}"/"${ECR_REPOSITORY}":"${App_Version}"
                echo "-------- Docker Image Pushed Successfully --------"
                '''
            }
        }
        stage("cleanup") {
            steps {
                sh '''
                   echo "-------- Cleaning Up Jenkins Machine --------"
                   docker image prune -a -f
                   echo "-------- Clean Up Successful --------"
                '''
            }
       }
       stage("Deployment Acceptance") {
           steps {
               input 'Trigger Deployment'
           }
       }
       stage('Deploy to EC2') {
            steps {
                script{
                    echo 'Deploying to EC2 instance...'
                    withCredentials([
                        sshUserPrivateKey(credentialsId: 'ssh-key', keyFileVariable: 'SSH_KEY'),
                        string(credentialsId: 'db-pass', variable: 'DB_PASS')
                    ]) {
                        sh '''
                            # Deploy using SSH
                            ssh -o StrictHostKeyChecking=no -i ${SSH_KEY_PATH} ${EC2_USER}@${EC2_HOST} << 'ENDSSH'
                            
                            # Set environment variables
                            export AWS_REGION=${AWS_REGION}
                            export ECR_REGISTRY=${ECR_REGISTRY}
                            export ECR_REPOSITORY=${ECR_REPOSITORY}
                            
                            # Login to ECR
                            echo "Logging into ECR on EC2..."
                            aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
                            
                            # Stop existing container
                            echo "Stopping existing container..."
                            docker stop php-app 2>/dev/null || true
                            docker rm php-app 2>/dev/null || true
                            
                            # Pull latest image
                            echo "Pulling latest image..."
                            docker pull ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
                            
                            # Run new container
                            echo "Starting new container..."
                            docker run -d \
                                -p 80:80 \
                                -e DB_HOST=${DB_HOST} \
                                -e DB_NAME=${DB_NAME} \
                                -e DB_USER=${DB_USER} \
                                -e DB_PASSWORD=${DB_PASSWORD} \
                                --name php-app \
                                --restart unless-stopped \
                                ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
                            
                            # Verify container is running
                            echo "Verifying deployment..."
                            sleep 10
                            docker ps | grep php-app
                            
                       ENDSSH
                        '''
                    }
                }
            }
        }
    }
}

