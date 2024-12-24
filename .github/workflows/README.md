# Overview
This repository contains deployment configurations for our application using Kubernetes. The deployment process is automated through GitHub Actions and triggers when changes are made to the `develop` (test environment) or `main` (production environment) branches.

### Environment Structure
- **Test Environment:** Triggered by changes to the `develop` branch.
- **Production Environment:** Triggered by changes to the `main` branch.

## GitHub Actions Workflow
The deployment process consists of two main jobs:

### 1. Build and Push
This job handles the Docker image creation and storage:
- Checks out the repository code.
- Authenticates with Docker Hub.
- Builds the application Docker image.
- Pushes the image to the Docker Hub repository.

### 2. Deploy
This job manages the actual deployment to our environments:
- Copies Kubernetes deployment files to the remote server using `appleboy/scp-action`.
- Connects to the remote server via SSH using `appleboy/ssh-action`.
- Updates the application using the copied Kubernetes configurations.

## Configuration Management
- Environment-specific secrets are managed through GitHub Secrets.
- The system can be enhanced by implementing environment-specific secret management.

## Future Improvements
- Add environment-specific secret management.
- Implement environment validation checks.