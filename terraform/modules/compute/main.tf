# Módulo COMPUTE: EC2 Ubuntu que instala Docker y arranca el contenedor
# de la Fase 1 apuntando a RDS. Aquí es donde Docker y Terraform se unen.

data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd-gp3/ubuntu-noble-24.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

resource "aws_instance" "web" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  subnet_id              = var.public_subnet_id
  vpc_security_group_ids = [var.web_sg_id]
  key_name               = var.key_name

  user_data = templatefile("${path.module}/user_data.sh.tpl", {
    repo_url    = var.repo_url
    db_host     = var.db_host
    db_name     = var.db_name
    db_user     = var.db_user
    db_password = var.db_password
  })

  # Si cambia el user_data, recrear la instancia
  user_data_replace_on_change = true

  tags = {
    Name = "${var.project_name}-web"
  }
}
