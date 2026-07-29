# AWS Cloud Infrastructure: Academia de Pintura — v2

![AWS](https://img.shields.io/badge/AWS-232F3E?style=for-the-badge&logo=amazonwebservices&logoColor=white)
![Terraform](https://img.shields.io/badge/Terraform-7B42BC?style=for-the-badge&logo=terraform&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/GitHub%20Actions-2088FF?style=for-the-badge&logo=githubactions&logoColor=white)
![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

**Docker · Terraform · CI/CD con GitHub Actions.**

Despliegue de una app de inventario (PHP + MySQL) en AWS, en **cuatro fases**:
contenerizar → infraestructura como código → automatizar → documentar.

## Roadmap

- [x] **Fase 1** — App PHP dockerizada (Docker Compose: app + MySQL)
- [x] **Fase 2** — Infraestructura en AWS con Terraform (VPC, RDS, EC2 + Docker)
- [x] **Fase 3** — CI/CD con GitHub Actions (validación de Terraform + build/push a GHCR)
- [x] **Fase 4** — Documentación

## Índice

- [Introducción](#introducción)
- [Fase 1 — App dockerizada (local)](#fase-1--app-dockerizada-local)
- [Fase 2 — Infraestructura en AWS con Terraform](#fase-2--infraestructura-en-aws-con-terraform)
  - [¿Por qué Terraform? CloudFormation vs Terraform](#por-qué-terraform-cloudformation-vs-terraform)
  - [Arquitectura](#arquitectura)
  - [Estructura y módulos](#estructura-y-módulos)
  - [Variables y despliegue](#variables-y-despliegue)
  - [Destroy — decisión FinOps](#destroy--decisión-finops)
- [Fase 3 — CI/CD con GitHub Actions](#fase-3--cicd-con-github-actions)
- [Fase 4 — Documentación](#fase-4--documentación)
  - [Seguridad](#seguridad)
  - [Lecciones aprendidas y mejoras futuras](#lecciones-aprendidas-y-mejoras-futuras)

---

## Introducción

Versión 2 de mi proyecto [aws-cloud-infrastructure-project](https://github.com/albert0fernandez/aws-cloud-infrastructure-project), donde practico las herramientas que estoy aprendiendo. La aplicación (un CRUD de recursos, aulas y usuarios) no cambia; **lo que evoluciona es cómo se construye y opera su infraestructura**:

- **v1** — infraestructura definida con **CloudFormation**.
- **v2** — misma arquitectura con **Terraform**, la app en **Docker** y una capa de **CI/CD**.

El repositorio está organizado por fases:

```
.
├── docker/            # Fase 1 — app dockerizada (Dockerfile, init.sql, docker-compose.yml)
├── terraform/         # Fase 2 — Infraestructura como Código (módulos: network · compute · database)
└── .github/workflows/ # Fase 3 — CI/CD (terraform.yml · docker.yml)
```

---

## Fase 1 — App dockerizada (local)

Con **Docker Compose** reproduzco en local el mismo par que luego habrá en AWS, en dos contenedores:

- **`app`** — `php:8.3-apache` (extensiones `mysqli`, `pdo_mysql`, `xsl`), sirve en el puerto 80.
- **`db`** — MySQL 8, se inicializa sola con `db/init.sql`.

Dos guiños a producción: la BD **no expone puertos** (aislada, como una RDS privada) y la app **espera al *healthcheck*** de MySQL antes de arrancar.

```bash
cd docker
cp .env.example .env
docker compose up -d --build
```

La app queda en **`http://localhost:8081`** (el compose mapea `8081:80`).

<img width="1091" height="98" alt="image" src="https://github.com/user-attachments/assets/e788e235-827c-4d91-b459-f49aee4b34a8" />

<img width="1628" height="344" alt="image" src="https://github.com/user-attachments/assets/50026b0b-1061-432e-838d-7f228dc8e915" />

---

## Fase 2 — Infraestructura en AWS con Terraform

Llevo esa arquitectura a AWS **sin clicar en la consola**: la describo en ficheros `.tf` y Terraform la crea (o la destruye) con un comando. Así es repetible, versionable en Git y fácil de eliminar.

### ¿Por qué Terraform? CloudFormation vs Terraform

Ambos son *Infraestructura como Código*, pero con diferencias que justifican el salto:

| Aspecto | <img src="https://github.com/user-attachments/assets/1b30b26d-7baf-49b8-998d-709dbaf2a04c" width="22" height="22" align="center" /> CloudFormation | <img src="https://github.com/user-attachments/assets/15751f6f-39cb-4f1e-a7dd-6a43a2e3fcc4" width="22" height="22" align="center" /> Terraform |
| :--- | :--- | :--- |
| **Lenguaje** | YAML/JSON, solo AWS | HCL, multi-proveedor |
| **Estado** | Lo gestiona AWS (*stacks*) | Fichero `terraform.tfstate` que gestionas tú |
| **Previsualización** | *Change sets* (paso aparte) | `terraform plan` nativo: ves los cambios antes de aplicar |
| **Reutilización** | *Nested stacks* | **Módulos** de primera clase |

**En resumen:** Terraform aporta un `plan` antes del `apply`, un estado explícito que hay que proteger y una modularidad que hace el código reutilizable.

### Arquitectura

Una **VPC** de dos capas: la **EC2** vive en subredes **públicas** (sirve la app); la **RDS**, en subredes **privadas** (sin Internet). Solo la EC2 habla con la base de datos, por el puerto 3306. Todo en **2 zonas de disponibilidad**.

```mermaid
flowchart TB
    Internet([Internet])
    subgraph VPC["VPC 10.2.0.0/16"]
        direction TB
        EC2["EC2 · subred pública<br/>Docker + app PHP · puerto 80"]
        RDS["RDS MySQL 8 · subred privada<br/>sin acceso desde Internet"]
        EC2 -->|"3306"| RDS
    end
    Internet -->|"Internet Gateway · puerto 80"| EC2
```

<img width="592" height="740" alt="Diagrama de arquitectura AWS" src="https://github.com/user-attachments/assets/2566d9ce-b8fb-4f8b-80d9-b9054362040f" />

El acceso lo controlan los ***security groups***: puerto **80** abierto, **22 (SSH)** solo desde tu IP y **3306** solo desde la capa web.

#### 🛠️ Recursos de AWS utilizados

| | Servicio | Categoría | Función |
|:---:|:---|:---|:---|
| 🌐 | **VPC** | Networking | Red aislada con subredes públicas y privadas (2 AZ). |
| 🚪 | **Internet Gateway** | Networking | Salida a Internet para las subredes públicas. |
| 🛡️ | **Security Groups** | Seguridad | Cortafuegos: 80 público, 22 tu IP, 3306 solo web. |
| 🖥️ | **EC2** | Computación | *Hosting* de la app PHP en un contenedor Docker. |
| 🗄️ | **RDS (MySQL 8)** | Base de datos | Base de datos relacional gestionada y privada. |

> Arquitectura **mínima y de coste ~0 € bajo demanda** (Free Tier): sin ELB/ASG, S3 ni Lambda.

### Estructura y módulos

El código se divide en **módulos** reutilizables (como funciones):

| Módulo | Qué crea |
| :--- | :--- |
| **`network`** | VPC, subredes públicas/privadas (2 AZ), Internet Gateway, rutas y *security groups*. |
| **`compute`** | EC2 Ubuntu 24.04 (`t3.micro`) + `user_data.sh.tpl`. |
| **`database`** | RDS MySQL 8 (`db.t3.micro`) privada. |

**`user_data.sh.tpl`** es donde **se unen Docker y Terraform**: al arrancar, la EC2 instala Docker, clona el repo, carga `init.sql` y levanta el contenedor apuntando a la RDS (por eso tras el `apply` conviene esperar ~3-4 min).

### Variables y despliegue

Dos variables son obligatorias y **nunca se suben al repo**: `admin_cidr` (tu IP en CIDR, para SSH) y `db_password` (contraseña maestra de la RDS). El resto tienen valores por defecto (`aws_region = eu-west-1`, Free Tier…). Crea `terraform.tfvars` a partir de `terraform.tfvars.example`.

```bash
export AWS_PROFILE=admin   # usuario IAM, no root
cd terraform
terraform init
terraform plan             # muestra qué se va a crear, sin tocar nada
terraform apply            # crea la infraestructura (~5-10 min por la RDS)
```

Al terminar verás los **outputs** (IP pública + endpoint RDS). Login de demostración: **`Administrador`** / **`MiClave@2026`**.

> **🔧 Un reto que resolví:** el login fallaba porque el `init.sql` traía *hashes* de contraseña desconocidos. Lo arreglé fijando una contraseña conocida y **recreando solo la EC2** con `terraform apply -replace="module.compute.aws_instance.web"`, sin tocar la RDS ni la red. Buen ejemplo de por qué el estado y la modularidad de Terraform importan: reconstruyes una pieza sin afectar a las demás.

### Destroy — decisión FinOps

AWS **cobra por hora**, así que la plataforma se levanta bajo demanda y se destruye al terminar:

```bash
export AWS_PROFILE=admin
cd terraform
terraform destroy
```

Destruida, el **coste es ~0 €**: el código queda en el repo y se vuelve a levantar idéntico en minutos.

```
Código .tf  →  apply  →  Infra en AWS  →  verificar  →  destroy
(describes)    (crea)     (existe/cobra)   (capturas)    (borra todo)
```

---

## Fase 3 — CI/CD con GitHub Actions

Cada cambio se valida y se empaqueta solo, sin pasos manuales. Los workflows viven en `.github/workflows/`:

| Workflow | Se dispara con… | Qué hace |
|---|---|---|
| **Terraform CI** | cambios en `terraform/**` | `fmt` → `init` → `validate`. Valida sintaxis **sin credenciales AWS**. |
| **Docker Build** | cambios en `docker/**` | construye la imagen y la sube a **GHCR** (`ghcr.io/albert0fernandez/academia-app`) con tags `latest` y SHA corto. |

Docker Build se autentica en GHCR con el `GITHUB_TOKEN` automático de Actions, sin claves propias. El estado se ve en los **badges** de arriba y en la pestaña **Actions**.

---

## Fase 4 — Documentación

**App en AWS** — inventario funcionando con la IP pública:

<img width="864" height="932" alt="App de inventario en AWS" src="https://github.com/user-attachments/assets/2ebd0469-070e-4329-84d3-8804aef48482" />

**Consola de AWS (`eu-west-1`)** — EC2 en *running* y RDS en *available*:

<img width="1088" height="311" alt="Consola EC2 en AWS" src="https://github.com/user-attachments/assets/ef5fea0d-5e29-49d6-bad0-0c1bf9903192" />

<img width="875" height="343" alt="Consola RDS en AWS" src="https://github.com/user-attachments/assets/2ce0e44f-6791-4eb1-b6b3-ab74cab8d115" />

### Seguridad

- **Secretos fuera del repo** — `tfvars`, `tfstate`, `.terraform/` y `tfplan` en `.gitignore`.
- **RDS privada** — sin IP pública; solo accesible desde la EC2.
- **SSH restringido** — puerto 22 solo desde tu IP.
- **Usuario IAM en vez de root** — root se reserva para facturación y emergencias.

### Lecciones aprendidas y mejoras futuras

**Aprendido:** el `plan` antes del `apply` da confianza; el estado es delicado (lleva secretos, no se sube); los módulos hacen el código reutilizable; y `user_data` une Terraform y Docker.

**Mejoras futuras:** alta disponibilidad (ALB + Auto Scaling en varias AZ), backend remoto del estado (S3 + DynamoDB), CI con `terraform plan` en cada PR (vía OIDC), secretos en AWS Secrets Manager, RDS Multi-AZ y permisos IAM de mínimo privilegio.
