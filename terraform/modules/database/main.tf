# Módulo DATABASE: RDS MySQL 8 en subredes privadas
# Equivale a la RDS de tu plantilla original, sin acceso desde internet

resource "aws_db_subnet_group" "main" {
  name_prefix = "${var.project_name}-"
  subnet_ids  = var.private_subnet_ids

  tags = {
    Name = "${var.project_name}-db-subnets"
  }
}

resource "aws_db_instance" "main" {
  identifier_prefix = "${var.project_name}-"

  engine         = "mysql"
  engine_version = "8.0"
  instance_class = "db.t3.micro" # elegible Free Tier

  allocated_storage = 20
  storage_type      = "gp3"

  db_name  = var.db_name
  username = var.db_user
  password = var.db_password

  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = [var.db_sg_id]
  publicly_accessible    = false
  multi_az               = false # Free Tier; true en produccion

  backup_retention_period = 0    # proyecto de aprendizaje; >0 en produccion
  skip_final_snapshot     = true # permite terraform destroy sin snapshot
  deletion_protection     = false

  tags = {
    Name = "${var.project_name}-rds"
  }
}
