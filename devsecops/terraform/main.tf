resource "aws_security_group" "foodgo_demo" {
  name        = "foodgo-ciap-demo"
  description = "Restricted HTTP and SSH access for FoodGo CIAP demonstration"
  vpc_id      = var.vpc_id

  ingress {
    description = "HTTP demo access"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = [var.ssh_cidr]
  }
  ingress {
    description = "SSH from student's IP only"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.ssh_cidr]
  }
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
  tags = { Project = "FoodGo-CIAP", ManagedBy = "Terraform" }
}

# This security group is a reusable starter. No billable EC2 instance is created by this configuration.
output "foodgo_security_group_id" {
  value       = aws_security_group.foodgo_demo.id
  description = "Security group ID to attach to a separately reviewed demo instance"
}
