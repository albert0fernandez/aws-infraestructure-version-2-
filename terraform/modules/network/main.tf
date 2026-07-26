# Módulo NETWORK: VPC, subredes públicas/privadas, IGW, rutas y Security Groups
# Equivale a la parte de red de tu IaC_Script_VPC_EC2_RDS.yaml

data "aws_availability_zones" "available" {
  state = "available"
}

resource "aws_vpc" "main" {
  cidr_block           = var.vpc_cidr
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name = "${var.project_name}-vpc"
  }
}

# ── Subredes públicas (2 AZ) — aquí vive la EC2 ────────────────
resource "aws_subnet" "public" {
  count = 2

  vpc_id                  = aws_vpc.main.id
  availability_zone       = data.aws_availability_zones.available.names[count.index]
  cidr_block              = cidrsubnet(var.vpc_cidr, 8, count.index) # 10.2.0.0/24, 10.2.1.0/24
  map_public_ip_on_launch = true

  tags = {
    Name = "${var.project_name}-public-${count.index + 1}"
  }
}

# ── Subredes privadas (2 AZ) — aquí vive la RDS ────────────────
resource "aws_subnet" "private" {
  count = 2

  vpc_id            = aws_vpc.main.id
  availability_zone = data.aws_availability_zones.available.names[count.index]
  cidr_block        = cidrsubnet(var.vpc_cidr, 8, count.index + 10) # 10.2.10.0/24, 10.2.11.0/24

  tags = {
    Name = "${var.project_name}-private-${count.index + 1}"
  }
}

# ── Internet Gateway y tabla de rutas pública ──────────────────
resource "aws_internet_gateway" "main" {
  vpc_id = aws_vpc.main.id

  tags = {
    Name = "${var.project_name}-igw"
  }
}

resource "aws_route_table" "public" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.main.id
  }

  tags = {
    Name = "${var.project_name}-rt-public"
  }
}

resource "aws_route_table_association" "public" {
  count = 2

  subnet_id      = aws_subnet.public[count.index].id
  route_table_id = aws_route_table.public.id
}

# ── Security Group WEB: 80 abierto, 22 solo desde tu IP ────────
resource "aws_security_group" "web" {
  name_prefix = "${var.project_name}-web-"
  description = "HTTP publico y SSH restringido"
  vpc_id      = aws_vpc.main.id

  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "SSH solo desde mi IP"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.admin_cidr]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-sg-web"
  }

  lifecycle {
    create_before_destroy = true
  }
}

# ── Security Group DB: 3306 SOLO desde el SG web ───────────────
resource "aws_security_group" "db" {
  name_prefix = "${var.project_name}-db-"
  description = "MySQL solo desde la capa web"
  vpc_id      = aws_vpc.main.id

  ingress {
    description     = "MySQL desde web"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.web.id]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-sg-db"
  }

  lifecycle {
    create_before_destroy = true
  }
}
