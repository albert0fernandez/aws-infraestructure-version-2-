# AWS Cloud Infrastructure: Academia de Pintura Version 2 



![AWS](https://img.shields.io/badge/AWS-232F3E?style=for-the-badge&logo=amazonwebservices&logoColor=white)
![Terraform](https://img.shields.io/badge/Terraform-7B42BC?style=for-the-badge&logo=terraform&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/GitHub%20Actions-2088FF?style=for-the-badge&logo=githubactions&logoColor=white)
![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

**Integración de Docker · Terraform · CI/CD con GitHub Actions.**

Despliegue completo de una aplicación de inventario (PHP + MySQL) sobre AWS, construido en
cuatro fases: primero la app se empaqueta en contenedores, luego la infraestructura de la
nube se define como código con Terraform, después se automatiza con integración continua y,
por último, se documenta todo el proceso.

## Roadmap

- [x] **Fase 1** — Aplicación PHP dockerizada (Docker Compose: app + MySQL)
- [x] **Fase 2** — Infraestructura en AWS con Terraform (VPC, RDS, EC2 + Docker)
- [x] **Fase 3** — CI/CD con GitHub Actions (validación de Terraform + build/push de la imagen a GHCR)
- [x] **Fase 4** — Documentación 

## Índice

- [Introducción](#introducción)
- [Fase 1 — Aplicación dockerizada (local)](#fase-1--aplicación-dockerizada-local)
- [Fase 2 — Infraestructura en AWS con Terraform](#fase-2--infraestructura-en-aws-con-terraform)
  - [¿Por qué Terraform? CloudFormation vs Terraform](#por-qué-terraform-cloudformation-vs-terraform)
  - [Arquitectura](#arquitectura)
  - [Estructura y módulos](#estructura-y-módulos)
  - [Variables necesarias](#variables-necesarias)
  - [Despliegue](#despliegue)
  - [Destroy — decisión FinOps](#destroy--decisión-finops)
- [Fase 3 — CI/CD con GitHub Actions](#fase-3--cicd-con-github-actions)
- [Fase 4 — Documentación](#fase-4--documentación)
  - [Seguridad](#seguridad)
  - [Lecciones aprendidas y mejoras futuras](#lecciones-aprendidas-y-mejoras-futuras)

---

## Introducción

Este proyecto es la **versión 2** de mi proyecto [aws-cloud-infrastructure-project](https://github.com/albert0fernandez/aws-cloud-infrastructure-project) en el cual he integrado y practicado con las herramientas que estoy aprendiendo actualmente.
La aplicación en sí (el CRUD de recursos, aulas, usuarios…) no cambia
respecto a la v1; **lo que evoluciona es cómo se construye y opera la infraestructura** que
la sostiene:

- **v1** — la infraestructura de AWS (VPC, subredes, EC2 y RDS) se definía con
  **CloudFormation**, la herramienta de *Infraestructura como Código* nativa de AWS.
- **v2 ** — la misma arquitectura, ahora descrita con **Terraform**, más el
  empaquetado de la app en **Docker** y una capa de **CI/CD** con GitHub Actions.

> 📎 **Repositorio de la v1 (CloudFormation):** [aws-cloud-infrastructure-project](https://github.com/albert0fernandez/aws-cloud-infrastructure-project)

El repositorio está organizado por fases:

```
.
├── docker/                     # Fase 1 — app dockerizada
│   ├── app/                    #   Dockerfile + código PHP de la app
│   ├── db/init.sql             #   esquema y datos iniciales de MySQL
│   └── docker-compose.yml      #   app + MySQL para levantar en local
├── terraform/                  # Fase 2 — Infraestructura como Código
│   ├── main.tf, providers.tf, variables.tf, outputs.tf
│   ├── terraform.tfvars.example#   plantilla de variables 
│   └── modules/                #   network · compute · database
└── .github/workflows/          # Fase 3 — CI/CD
    ├── terraform.yml           #   Terraform CI
    └── docker.yml              #   Docker Build → GHCR
```

---

## Fase 1 — Aplicación dockerizada (local)

La primera fase reproduce en local, con **Docker Compose**, el mismo par que luego habrá en
AWS: la aplicación web y su base de datos, en dos contenedores separados.

- **`app`** — imagen basada en `php:8.3-apache` con las extensiones necesarias (`mysqli`,
  `pdo_mysql`, `xsl`). Copia el código de la aplicación y sirve en el puerto 80.
- **`db`** — MySQL 8 que se inicializa automáticamente con `db/init.sql` (esquema + datos).

Dos decisiones reproducen el comportamiento de producción:
- La base de datos **no expone puertos** al exterior, igual que una RDS privada: solo la app
  puede hablar con ella por la red interna de Docker.
- La app **espera al *healthcheck*** de MySQL antes de arrancar, para no fallar si la base
  todavía no está lista.

### Cómo ejecutarlo en local

```bash
cd docker
cp .env.example .env      # ajusta credenciales si quieres
docker compose up -d --build
```

La app queda en `http://localhost:8081` (el `docker-compose.yml` mapea el puerto host
`8081` al `80` del contenedor) y la base se carga sola desde `db/init.sql`.

<img width="1091" height="98" alt="image" src="https://github.com/user-attachments/assets/e788e235-827c-4d91-b459-f49aee4b34a8" />

<img width="1628" height="344" alt="image" src="https://github.com/user-attachments/assets/50026b0b-1061-432e-838d-7f228dc8e915" />

---

## Fase 2 — Infraestructura en AWS con Terraform

La segunda fase lleva esa misma arquitectura a AWS, pero **sin crearla a mano** en la
consola: se describe en ficheros `.tf` y Terraform la crea (o la destruye) con un solo
comando. Esto la hace repetible, versionable en Git y fácil de eliminar cuando no se usa.

### ¿Por qué Terraform? CloudFormation vs Terraform

El cambio de fondo respecto a la v1 es sustituir CloudFormation por Terraform. Ambos son
*Infraestructura como Código*, pero con diferencias que justifican el salto:

| Aspecto | CloudFormation <img width="64" height="64" alt="image" src="https://github.com/user-attachments/assets/31ee8976-98e3-49ec-9455-fa401d24b3de" />
 | Terraform <img width="64" height="64" alt="image" src="https://github.com/user-attachments/assets/51e7e37b-beb2-4bf7-84d6-99740f9e5d89" />
 |
|---|---|---|
| **Lenguaje** | YAML/JSON, específico de AWS | HCL, más legible y expresivo |
| **Alcance** | Solo AWS | Multi-proveedor (AWS, Azure, GCP…) |
| **Estado** | Lo gestiona AWS internamente (*stacks*), no ves el fichero | Fichero `terraform.tfstate` que gestionas tú (local o remoto) |
| **Previsualización** | *Change sets* (existe, pero es un paso aparte) | `terraform plan` nativo: ves los cambios **antes** de aplicar |
| **Reutilización** | *Nested stacks*, algo rígidas | **Módulos** de primera clase (`network`, `compute`, `database`) |
| **Comunidad** | Recursos y ejemplos de AWS | *Registry* con miles de módulos y *providers* |

**Qué se aprende con cada uno:** CloudFormation enseña el modelo de recursos de AWS "puro",
sin capas intermedias; Terraform aporta un flujo de trabajo más seguro (el `plan` antes del
`apply`), un estado explícito que hay que entender y proteger, y una modularidad que hace el
código reutilizable y fácil de razonar.

### Arquitectura

Una **VPC** con dos capas. La **EC2** vive en subredes **públicas** (tiene IP pública y sirve
la app); la **RDS** vive en subredes **privadas** (sin salida a Internet). La EC2 es la única
que puede hablar con la base de datos, por el puerto 3306.

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

Todo se despliega en **2 zonas de disponibilidad** (subredes redundantes). El control de
acceso lo hacen los ***security groups*** (cortafuegos):

- **web** — puerto **80** abierto a todo Internet; **22 (SSH)** solo desde tu IP.
- **db** — puerto **3306** abierto **únicamente** desde el *security group* web.

#### 🛠️ Recursos de AWS utilizados

| | Servicio | Categoría | Función en el proyecto |
|:---:|:---|:---|:---|
| 🌐 | **VPC** | Networking | Red aislada con subredes públicas y privadas (2 AZ). |
| 🚪 | **Internet Gateway** | Networking | Salida a Internet para las subredes públicas. |
| 🛡️ | **Security Groups** | Seguridad | Cortafuegos: 80 público, 22 solo tu IP, 3306 solo desde la web. |
| 🖥️ | **EC2** | Computación | *Hosting* de la aplicación PHP en un contenedor Docker. |
| 🗄️ | **RDS (MySQL 8)** | Base de datos | Base de datos relacional gestionada y privada. |

> A diferencia de la v1, esta versión mantiene una arquitectura **mínima y de coste ~0 €
> bajo demanda** (Free Tier): no incluye ELB/ASG, S3 ni Lambda.

### Estructura y módulos

El código está dividido en **módulos** reutilizables, igual que en programación se separa el
código en funciones. Cada módulo recibe sus variables, crea sus recursos y devuelve sus
salidas:

| Módulo | Qué crea |
| :--- | :--- |
| **`network`** | VPC (`10.2.0.0/16`), subredes públicas/privadas (2 AZ), Internet Gateway, tablas de rutas y los *security groups*. |
| **`compute`** | EC2 Ubuntu 24.04 (`t3.micro`, Free Tier) + `user_data.sh.tpl`. |
| **`database`** | RDS MySQL 8 (`db.t3.micro`, Free Tier) privada, en las subredes privadas. |

El fichero **`user_data.sh.tpl`** es donde **se unen Docker y Terraform**: es el script que
la EC2 ejecuta al arrancar. Instala Docker, clona este repositorio, espera a que la RDS
acepte conexiones, carga `init.sql`, fija una contraseña conocida para el usuario
`Administrador` y, por último, construye la imagen y levanta el contenedor apuntando a la RDS.
Por eso, tras el `apply`, conviene dar ~3-4 min extra a que ese arranque termine.

### Variables necesarias

Crea `terraform/terraform.tfvars` a partir de `terraform.tfvars.example`. Las dos primeras son
obligatorias (no tienen valor por defecto) y **nunca se suben al repositorio**:

| Variable | Obligatoria | Descripción |
|---|---|---|
| `admin_cidr` | ✅ | Tu IP pública en CIDR (ej. `1.2.3.4/32`) para el acceso SSH |
| `db_password` | ✅ | Contraseña maestra de la RDS |
| `aws_region` | — | Por defecto `eu-west-1` |
| `project_name` | — | Por defecto `retacantabria-v2` |
| `instance_type` / `db_name` / `db_user` | — | Valores por defecto elegibles Free Tier |

### Despliegue

```bash
export AWS_PROFILE=admin       # usa el usuario IAM, no root (ver sección Seguridad)
cd terraform
terraform init                 # descarga el proveedor de AWS (solo la 1ª vez)
terraform plan                 # muestra qué se va a crear, sin tocar nada
terraform apply                # crea la infraestructura REAL (~5-10 min por la RDS)
```

Al terminar, Terraform muestra los **outputs**: la IP pública de la EC2 y el *endpoint* de la
RDS. Cuando el `user_data` acabe (~3-4 min), la app estará disponible en `http://<IP-pública>`.

Acceso a la aplicación (usuario de demostración): **`Administrador`** / **`MiClave@2026`**.

<!-- 📸 CAPTURA: final del `apply` con los outputs -->

> **🔧 Un reto que resolví:** en el primer despliegue el login fallaba porque el `init.sql`
> del reto traía *hashes* de contraseña desconocidos. Lo resolví fijando una contraseña
> conocida (un `UPDATE` en `init.sql` y en el `user_data`) y **recreando solo la instancia
> EC2** con `terraform apply -replace="module.compute.aws_instance.web"`, sin tocar la RDS ni
> el resto de la red. Es un buen ejemplo de por qué el estado y la modularidad de Terraform
> importan: puedes reconstruir una pieza sin afectar a las demás.

### Destroy — decisión FinOps

Como AWS **cobra por hora** mientras los recursos existen, la plataforma se levanta **bajo
demanda** y se destruye al terminar:

```bash
export AWS_PROFILE=admin
cd terraform
terraform destroy              # borra EC2, RDS, VPC y todo lo creado
```

Con la infraestructura destruida el **coste es ~0 €**: el código Terraform queda en el repo y
se puede volver a levantar todo idéntico en minutos cuando haga falta. Terraform sabe qué
borrar gracias al fichero de estado `terraform.tfstate`.

```
Código .tf  →  apply  →  Infra REAL en AWS  →  verificar  →  destroy
(describes)    (crea)     (existe y cobra)      (capturas)    (borra todo)
```

---

## Fase 3 — CI/CD con GitHub Actions

La tercera fase automatiza la validación y la construcción: cada cambio en el repositorio se
comprueba y se empaqueta solo, sin pasos manuales. Los workflows viven en `.github/workflows/`.

| Workflow | Fichero | Se dispara cuando… | Qué hace |
|---|---|---|---|
| **Terraform CI** | `terraform.yml` | cambia algo en `terraform/**` (push o PR) | `fmt -check` → `init -backend=false` → `validate`. Comprueba formato y sintaxis **sin necesitar credenciales AWS** |
| **Docker Build** | `docker.yml` | cambia algo en `docker/**` (push) | construye la imagen (`docker/app`) y, si el build pasa, la sube a **GHCR** como `ghcr.io/albert0fernandez/academia-app` con los tags `latest` y el SHA corto |

Detalles de diseño:
- **Terraform CI no usa credenciales de AWS**: validar el formato y la sintaxis no requiere
  conectarse a la nube, así que el workflow es seguro y rápido.
- **Docker Build se autentica en GHCR con el `GITHUB_TOKEN`** automático de Actions (permiso
  `packages: write`), sin claves estáticas propias.

El estado de ambos se ve en los **badges** del principio de este README y en la pestaña
**Actions** del repositorio.

<!-- 📸 CAPTURA: pestaña Actions con ambos workflows en verde -->

---

## Fase 4 — Documentación

La cuarta fase es esta propia documentación, más las capturas del proyecto en funcionamiento.

**App en AWS** — inventario funcionando con la IP pública:

<img width="864" height="932" alt="App de inventario en AWS" src="https://github.com/user-attachments/assets/2ebd0469-070e-4329-84d3-8804aef48482" />

**Consola de AWS (región `eu-west-1`)** — EC2 en *running* y RDS en *available*:

<img width="1088" height="311" alt="Consola EC2 en AWS" src="https://github.com/user-attachments/assets/ef5fea0d-5e29-49d6-bad0-0c1bf9903192" />

<img width="875" height="343" alt="Consola RDS en AWS" src="https://github.com/user-attachments/assets/2ce0e44f-6791-4eb1-b6b3-ab74cab8d115" />

### Seguridad

Buenas prácticas aplicadas en el proyecto:

- **Secretos fuera del repo** — `terraform.tfvars`, `terraform.tfstate`, `.terraform/` y
  `tfplan` están en `.gitignore`. La contraseña de la RDS nunca se versiona.
- **RDS privada** — sin IP pública; solo accesible desde la EC2 por el puerto 3306.
- **SSH restringido** — el puerto 22 solo se abre desde tu IP (`admin_cidr`).
- **Usuario IAM en vez de root** — el trabajo diario (Terraform, CLI) se hace con un usuario
  IAM con permisos definidos; la cuenta *root* se reserva para facturación y emergencias.
- **GHCR con `GITHUB_TOKEN`** — la CI publica la imagen sin claves estáticas propias.

### Lecciones aprendidas y mejoras futuras

**Lecciones aprendidas**
- `terraform plan` antes de `apply` da confianza: ves exactamente qué va a cambiar.
- El **estado** (`terraform.tfstate`) es delicado: contiene secretos y nunca debe subirse.
- Separar en **módulos** (`network`/`compute`/`database`) hace el código legible y reutilizable.
- El patrón `user_data` une Terraform y Docker: la EC2 se autoconfigura al arrancar.

**Mejoras futuras**
- **Alta disponibilidad**: ALB + Auto Scaling Group en varias AZ, en vez de una única EC2.
- **Backend remoto del estado**: `terraform.tfstate` en S3 con bloqueo en DynamoDB.
- **CI con `terraform plan`**: comentar el plan en cada PR (credenciales vía OIDC, sin claves estáticas).
- **Gestión de secretos**: mover la contraseña de la RDS a AWS Secrets Manager.
- **RDS Multi-AZ** y backups automáticos para un escenario de producción.
- **Permisos IAM de mínimo privilegio** en lugar de `AdministratorAccess`.
