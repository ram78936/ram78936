# FoodGo DevSecOps CIAP starter

This package preserves the original PHP pages and adds a starter CI/CD setup for the existing FoodGo microproject.

## Local Docker run
1. Install Docker Desktop and start it.
2. Copy `.env.example` to `.env` and replace both sample values with strong, different passwords.
3. Run `docker compose up --build -d`.
4. Open `http://localhost:8080`.
5. Register a test account, sign in, browse restaurants/menu, and test cart/checkout with demo data.
6. View logs with `docker compose logs -f web` and stop with `docker compose down`.

The SQL initialization runs only when the MySQL data volume is first created. To reset this disposable demo database, run `docker compose down -v` and then `docker compose up --build -d`; this deletes the database volume.

## Jenkins and security tools
- Jenkins needs Docker CLI/daemon access, PHP CLI, and Trivy installed on the agent.
- The Jenkinsfile runs PHP syntax checks, Trivy filesystem scans, builds an image, then scans the image. Trivy is set to report findings without failing the pipeline (`--exit-code 0`) so initial results can be reviewed; change this to a policy gate after baseline remediation.
- `sonar-project.properties` is a starter project configuration. Configure a SonarQube server and scanner in Jenkins and run the scanner with a securely stored token; do not commit tokens.
- Capture the actual console logs, SonarQube dashboard, and Trivy reports for the assignment. This package does not claim scans have already been run.

## Terraform
`devsecops/terraform` creates a restricted AWS security group only. It intentionally does not create a billable EC2 instance. Fill in a valid VPC ID and your own public IP `/32` in a local tfvars file, then run `terraform init`, `terraform fmt -check`, `terraform validate`, `terraform plan`. Review the plan before any `terraform apply`. AWS resources may incur charges.

## Important security notes
- Never commit `.env`, passwords, database dumps containing personal data, or scanner tokens.
- The supplied SQL schema and menu seed data are for a demo. Do not use this starter as a production deployment without further review (TLS, secure cookie/session settings, CSRF protections, input/output validation, authorization checks, backups, monitoring, and hardened secrets management).
- FoodGo currently uses `password_hash`/`password_verify` and prepared statements in the inspected registration/login code; review all remaining queries and form handlers as part of the security assessment.
