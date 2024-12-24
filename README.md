# **E-Commerce Application on EC2**

This project demonstrates deploying an e-commerce application on Amazon Elastic Compute Cloud (EC2) using **Terraform** and **GitHub Actions**.

## Project Structure:
- **`deployments/`**: Contains the Kubernetes YAML files.
- **`terraform/`**: Contains the Terraform scripts to build the project's infrastructure.
- **`workflows/`**: Contains the GitHub Actions script to deploy the application.

## Highlights:

    **Backend**: Laravel application
    **Database**: MySQL (Amazon RDS)
    **Web Server**: Nginx for reverse proxy and static file serving.
    **Infrastructure**:
        - AWS EC2 instances for the application and Nginx.
        - Amazon RDS for MySQL database.
        - Dockerhub for container images

## Deployment Steps:
- Clone the repository
- Configure Terraform
    - Navigate to the terraform directory
    - Update the main.tf file with your aws properties
    - Run **_terraform init_** to intialize the porject, **_terraform plan_**, **_terraform apply_** to run the script
- push your code changes to the **_develop or main_** branches. This will trigger a GitHub Actions workflow to build and deploy the application to the AWS environment. 



## KEY DESIGN DECISION
- **CI/CD Integration**:
    - Automated build and containerization using GitHub Actions and Docker for seamless deployment.

 - **Separation of Nginx and Application**:
    - Decoupled Nginx and the Laravel application for improved scalability and management.

 - **Reverse Proxy with Nginx**:
    - Nginx handles reverse proxying requests to the application and will manage HTTPS traffic once SSL is configured.

- **Use of EC2 Over Managed Services:**
    - Based on project requirements, EC2.

- **Autoscaling**:
    - Added horizontal autoscaling policies in Terraform to handle varying loads.

- 
**NOTE** 
- Ensure you update the github action secrets.
- Ensure to update the terraform script.
- **Application Design**:
    - Due to earlier design decisions, the frontend and backend were built as a single unit, making them harder to separate at this stage.
    
