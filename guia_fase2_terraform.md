# Guía Fase 2 — Terraform (paso a paso)

El kit contiene la infraestructura completa en Terraform, **modular**, replicando tu arquitectura v1: VPC con subredes públicas/privadas y security groups (`network`), RDS MySQL privada (`database`) y una EC2 cuyo `user_data` instala Docker, clona tu repo, carga tu `init.sql` en RDS y arranca **el mismo contenedor de la Fase 1** (`compute`). Ese user_data es la unión Docker+Terraform: el corazón del proyecto.

```
terraform/
├── providers.tf            # AWS provider ~>5.0, región eu-west-1
├── variables.tf            # variables raíz (db_password es sensitive)
├── main.tf                 # conecta los 3 módulos
├── outputs.tf              # URL de la app, IP EC2, endpoint RDS
├── terraform.tfvars.example
└── modules/
    ├── network/            # VPC 10.2.0.0/16, 2+2 subredes, IGW, SGs
    ├── database/           # RDS MySQL 8 (db.t3.micro, privada)
    └── compute/            # EC2 Ubuntu 24.04 + user_data.sh.tpl
```

## Paso 0 — Requisitos (una vez)

**a) Terraform** (en el servidor Oracle o en WSL, donde prefieras trabajar):

```bash
wget -O - https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install -y terraform
terraform -version
```

**b) Credenciales AWS**: si ya hiciste la guía del AWS CLI (`aws login`), comprueba con `aws sts get-caller-identity`. Si caducaron (12 h), repite `aws login --region eu-west-1`.

**c) Presupuesto**: ten creada la alerta en AWS Budgets (5 €). La RDS y la EC2 son elegibles Free Tier, pero la regla es: **desplegar → capturar → destruir el mismo día**.

## Paso 1 — Colocar los archivos

Descomprime el zip en la raíz del repo (te queda `terraform/` junto a `docker/`) y añade esto al `.gitignore`:

```gitignore
*.tfvars
!*.tfvars.example
```

## Paso 2 — Tus valores

```bash
cd terraform
cp terraform.tfvars.example terraform.tfvars
nano terraform.tfvars
```

- `admin_cidr`: tu IP pública + `/32` (mírala con `curl ifconfig.me`). Es la única IP que podrá entrar por SSH.
- `db_password`: inventa una **sin `@`, `/`, `"` ni espacios** (limitación de RDS).
- `key_name`: solo si quieres SSH a la EC2 (un key pair que ya exista en eu-west-1). Puedes dejarlo comentado.

## Paso 3 — El ciclo Terraform (memorízalo, es lo que te preguntarán)

```bash
terraform init        # descarga el provider AWS
terraform fmt -recursive   # formatea
terraform validate    # sintaxis y coherencia
terraform plan        # QUÉ va a crear (repásalo: ~15 recursos)
```

Lee el plan con calma: verás la VPC, 4 subredes, IGW, rutas, 2 SGs, subnet group, RDS y EC2. Cuando lo entiendas:

```bash
terraform apply       # escribe "yes"
```

⏱️ **Paciencia: la RDS tarda 5-10 min.** Al acabar verás los outputs:

```
app_url      = "http://X.X.X.X"
rds_endpoint = "...eu-west-1.rds.amazonaws.com"
```

## Paso 4 — Verificar

Espera **3-4 minutos más** tras el apply (el user_data está instalando Docker, cargando tu BD en RDS y construyendo la imagen). Luego abre `app_url` en el navegador → login de tu app.

Si no carga: entra por SSH (`ssh -i tu.key ubuntu@IP`) y mira el log del arranque:

```bash
cat /var/log/user-data.log
```

## Paso 5 — Evidencias y limpieza (¡el mismo día!)

1. Capturas: la app servida desde la IP pública de AWS, la consola de RDS, el final del `terraform apply` con los outputs.
2. Commit del código:
```bash
git add terraform/ .gitignore && git commit -m "feat: infraestructura AWS con Terraform (VPC, RDS, EC2+Docker)" && git push
```
3. **Destruye todo:**
```bash
terraform destroy     # escribe "yes"
```
4. Comprueba en la consola de AWS (EC2, RDS, VPC en eu-west-1) que no queda nada.

## Errores típicos

| Error | Causa | Solución |
| --- | --- | --- |
| `InvalidClientTokenId` / `ExpiredToken` | Credenciales caducadas | `aws login --region eu-west-1` |
| `Error creating DB Instance: InvalidParameterValue ... MasterUserPassword` | Contraseña con `@` o `/` | Cámbiala en `terraform.tfvars` |
| La web no carga tras 5 min | user_data aún trabajando o falló | SSH → `cat /var/log/user-data.log` |
| App carga pero "sin conexión" a BD | RDS aún inicializando o SG mal | Espera 2 min; revisa que el SG db permite 3306 desde el SG web |
| `git clone` falla en user_data | El repo de GitHub es privado | Hazlo público (el user_data lo clona sin credenciales) |

## Para el README (sección Fase 2)

Marca el checkbox de la Fase 2 en el roadmap, añade las capturas del despliegue real y una tabla corta CloudFormation vs Terraform (sintaxis HCL vs YAML, `plan` antes de aplicar, estado, módulos reutilizables). Y documenta el destroy como decisión FinOps: "la infraestructura se levanta bajo demanda; coste del despliegue de evidencias: ~0 €".
