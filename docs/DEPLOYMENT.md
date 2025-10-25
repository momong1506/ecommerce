# E-Commerce Platform - AWS Deployment Guide (100% FREE TIER)

This guide will help you deploy the E-Commerce Platform to AWS using only **FREE TIER** resources, ensuring **ZERO cost** for 12 months.

## Table of Contents

- [Prerequisites](#prerequisites)
- [AWS Free Tier Resources](#aws-free-tier-resources)
- [Cost Monitoring](#cost-monitoring)
- [Deployment Methods](#deployment-methods)
- [Manual Deployment](#manual-deployment)
- [GitHub Actions Deployment](#github-actions-deployment)
- [Post-Deployment Steps](#post-deployment-steps)
- [Troubleshooting](#troubleshooting)

## Prerequisites

### AWS Account

1. **Create AWS Account** (if you don't have one):
   - Go to https://aws.amazon.com
   - Click "Create an AWS Account"
   - Complete the registration (requires credit card for verification, but won't be charged if you stay within Free Tier)

2. **AWS CLI Installation**:
   ```bash
   # macOS
   brew install awscli

   # Linux
   curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
   unzip awscliv2.zip
   sudo ./aws/install

   # Windows
   # Download from: https://awscli.amazonaws.com/AWSCLIV2.msi
   ```

3. **Configure AWS CLI**:
   ```bash
   aws configure
   # AWS Access Key ID: (your access key)
   # AWS Secret Access Key: (your secret key)
   # Default region name: us-east-1
   # Default output format: json
   ```

### Create EC2 Key Pair

```bash
# Create a key pair for SSH access
aws ec2 create-key-pair \
  --key-name ecommerce-key \
  --query 'KeyMaterial' \
  --output text > ~/.ssh/ecommerce-key.pem

# Set correct permissions
chmod 400 ~/.ssh/ecommerce-key.pem
```

## AWS Free Tier Resources

### What's Included (100% FREE for 12 months)

| Resource | FREE TIER Allocation | Our Usage |
|----------|---------------------|-----------|
| **EC2** | 750 hours/month of t2.micro | 1 instance (24/7) = 744 hours/month |
| **RDS** | 750 hours/month of db.t2.micro | 1 instance (24/7) = 744 hours/month |
| **RDS Storage** | 20 GB | 20 GB MySQL database |
| **EBS** | 30 GB | 8 GB for EC2 root volume |
| **Data Transfer** | 15 GB/month outbound | Normal usage < 15 GB |
| **Elastic IP** | 1 free when attached | 1 IP for EC2 |
| **CloudWatch** | 10 metrics, 10 alarms | Basic monitoring |
| **S3** | 5 GB storage, 20,000 GET, 2,000 PUT | CloudFormation templates only |

### What's NOT Used (to stay 100% FREE)

- ❌ **AWS SES** (replaced with MailHog - completely free)
- ❌ **NAT Gateway** ($0.045/hour - using public subnets instead)
- ❌ **Application Load Balancer** ($0.0225/hour - using direct EC2 access)
- ❌ **Multi-AZ RDS** (Single AZ only for FREE tier)
- ❌ **RDS Snapshots beyond backup retention** (7-day auto backups are free)

## Cost Monitoring

### Set Up Billing Alarms (IMPORTANT!)

1. **Enable Billing Alerts**:
   - Go to AWS Console → Billing Dashboard
   - Click "Billing Preferences"
   - Check "Receive Free Tier Usage Alerts"
   - Check "Receive Billing Alerts"
   - Enter your email address
   - Save preferences

2. **Create $1 Billing Alarm**:
   ```bash
   # This will alert you if costs exceed $1
   aws cloudwatch put-metric-alarm \
     --alarm-name billing-alarm-1-dollar \
     --alarm-description "Alert when AWS charges exceed $1" \
     --metric-name EstimatedCharges \
     --namespace AWS/Billing \
     --statistic Maximum \
     --period 21600 \
     --evaluation-periods 1 \
     --threshold 1.0 \
     --comparison-operator GreaterThanThreshold \
     --dimensions Name=Currency,Value=USD \
     --region us-east-1
   ```

3. **Monitor Free Tier Usage**:
   - Go to AWS Console → Billing Dashboard → Free Tier
   - Check usage regularly to ensure you're within limits

## Deployment Methods

### Option 1: Manual Deployment (Recommended for first time)

### Option 2: GitHub Actions (Automated CI/CD)

## Manual Deployment

### Step 1: Deploy Network Stack

```bash
cd infrastructure/cloudformation

aws cloudformation create-stack \
  --stack-name ecommerce-network \
  --template-body file://network.yaml \
  --parameters ParameterKey=EnvironmentName,ParameterValue=ecommerce \
  --region us-east-1

# Wait for completion (takes ~3 minutes)
aws cloudformation wait stack-create-complete \
  --stack-name ecommerce-network \
  --region us-east-1
```

### Step 2: Deploy Database Stack

```bash
# Set your database password
DB_PASSWORD="YourSecurePassword123"

aws cloudformation create-stack \
  --stack-name ecommerce-database \
  --template-body file://database.yaml \
  --parameters \
    ParameterKey=EnvironmentName,ParameterValue=ecommerce \
    ParameterKey=DBName,ParameterValue=ecommerce_db \
    ParameterKey=DBUsername,ParameterValue=ecommerce_user \
    ParameterKey=DBPassword,ParameterValue=$DB_PASSWORD \
  --region us-east-1

# Wait for completion (takes ~10-15 minutes)
aws cloudformation wait stack-create-complete \
  --stack-name ecommerce-database \
  --region us-east-1
```

### Step 3: Deploy Compute Stack

```bash
aws cloudformation create-stack \
  --stack-name ecommerce-compute \
  --template-body file://compute.yaml \
  --parameters \
    ParameterKey=EnvironmentName,ParameterValue=ecommerce \
    ParameterKey=KeyPairName,ParameterValue=ecommerce-key \
  --capabilities CAPABILITY_NAMED_IAM \
  --region us-east-1

# Wait for completion (takes ~3-5 minutes)
aws cloudformation wait stack-create-complete \
  --stack-name ecommerce-compute \
  --region us-east-1
```

### Step 4: Get Deployment Information

```bash
# Get EC2 Public IP
EC2_IP=$(aws cloudformation describe-stacks \
  --stack-name ecommerce-compute \
  --query 'Stacks[0].Outputs[?OutputKey==`PublicIP`].OutputValue' \
  --output text \
  --region us-east-1)

# Get RDS Endpoint
RDS_ENDPOINT=$(aws cloudformation describe-stacks \
  --stack-name ecommerce-database \
  --query 'Stacks[0].Outputs[?OutputKey==`DatabaseEndpoint`].OutputValue' \
  --output text \
  --region us-east-1)

echo "EC2 Public IP: $EC2_IP"
echo "RDS Endpoint: $RDS_ENDPOINT"
```

### Step 5: Deploy Application

```bash
# SSH into EC2 instance
ssh -i ~/.ssh/ecommerce-key.pem ec2-user@$EC2_IP

# Clone repository
cd ~
git clone <your-repo-url> app
cd app

# Configure backend .env
cd backend
cp .env.example .env

# Update database credentials
sed -i "s/DB_HOST=db/DB_HOST=$RDS_ENDPOINT/" .env
sed -i "s/DB_PASSWORD=ecommerce_password/DB_PASSWORD=$DB_PASSWORD/" .env

# Configure frontend .env
cd ../frontend
cp .env.example .env
sed -i "s|http://localhost:8000|http://$EC2_IP:8000|" .env

# Start application
cd ../infrastructure
sudo docker-compose up -d --build

# Run migrations
sudo docker-compose exec backend php artisan migrate --force
sudo docker-compose exec backend php artisan db:seed --force

# Start queue worker
sudo docker-compose exec -d backend php artisan queue:work
```

## GitHub Actions Deployment

### Step 1: Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and Variables → Actions

Add the following secrets:

```
AWS_ACCESS_KEY_ID=<your-access-key>
AWS_SECRET_ACCESS_KEY=<your-secret-key>
AWS_REGION=us-east-1
AWS_ACCOUNT_ID=<your-account-id>
EC2_KEY_PAIR_NAME=ecommerce-key
EC2_PRIVATE_KEY=<contents of ecommerce-key.pem>
DB_PASSWORD=<your-database-password>
RDS_ENDPOINT=<your-rds-endpoint>
```

### Step 2: Trigger Deployment

```bash
# Push to main branch (triggers CI)
git push origin main

# Manual deployment via GitHub Actions
# Go to Actions → Deploy to AWS → Run workflow
```

## Post-Deployment Steps

### 1. Verify Deployment

```bash
# Check if all containers are running
ssh -i ~/.ssh/ecommerce-key.pem ec2-user@$EC2_IP
sudo docker-compose ps

# Expected output:
# backend         running    0.0.0.0:8000->9000/tcp
# frontend        running    0.0.0.0:5173->5173/tcp
# db              running    3306/tcp
# mailhog         running    0.0.0.0:8025->8025/tcp
```

### 2. Access Application

- **Frontend**: http://YOUR_EC2_IP:5173
- **Backend API**: http://YOUR_EC2_IP:8000/api/v1
- **MailHog**: http://YOUR_EC2_IP:8025
- **Health Check**: http://YOUR_EC2_IP:8000/up

### 3. Test the Application

1. Open frontend URL
2. Browse products
3. Add products to cart
4. Complete checkout
5. Check MailHog for order confirmation email

### 4. Start Queue Worker (for emails)

```bash
ssh -i ~/.ssh/ecommerce-key.pem ec2-user@$EC2_IP
cd app/infrastructure
sudo docker-compose exec -d backend php artisan queue:work
```

## Troubleshooting

### EC2 Instance Not Accessible

```bash
# Check security group allows port 22, 80, 8000, 5173
aws ec2 describe-security-groups \
  --filters Name=group-name,Values=ecommerce-ec2-sg \
  --region us-east-1
```

### Database Connection Failed

```bash
# Verify RDS is running
aws rds describe-db-instances \
  --db-instance-identifier ecommerce-mysql \
  --region us-east-1

# Check security group allows port 3306 from EC2
```

### Docker Containers Not Starting

```bash
ssh -i ~/.ssh/ecommerce-key.pem ec2-user@$EC2_IP

# Check Docker logs
cd app/infrastructure
sudo docker-compose logs backend
sudo docker-compose logs frontend

# Restart containers
sudo docker-compose restart
```

### Application Not Loading

```bash
# Check if ports are listening
sudo netstat -tlnp | grep -E '8000|5173'

# Check Laravel logs
sudo docker-compose exec backend tail -f storage/logs/laravel.log
```

## Cleanup (Delete All Resources)

**⚠️ WARNING**: This will delete ALL resources and data!

```bash
# Delete stacks in reverse order
aws cloudformation delete-stack --stack-name ecommerce-compute --region us-east-1
aws cloudformation delete-stack --stack-name ecommerce-database --region us-east-1
aws cloudformation delete-stack --stack-name ecommerce-network --region us-east-1

# Delete key pair
aws ec2 delete-key-pair --key-name ecommerce-key --region us-east-1
rm ~/.ssh/ecommerce-key.pem
```

## Free Tier Expiration

After 12 months, your Free Tier benefits expire. Costs will be approximately:

- **EC2 t2.micro**: ~$8.50/month
- **RDS db.t2.micro**: ~$12.00/month
- **Data Transfer**: ~$1-5/month (depending on usage)
- **Total**: ~$20-25/month

**Recommendation**: Delete resources before Free Tier expires if not needed.

## Support

If you encounter issues:

1. Check CloudWatch Logs
2. Review CloudFormation events
3. Check AWS Free Tier usage dashboard
4. Refer to SETUP.md for local development

## Resources

- [AWS Free Tier](https://aws.amazon.com/free/)
- [AWS CloudFormation Documentation](https://docs.aws.amazon.com/cloudformation/)
- [EC2 User Guide](https://docs.aws.amazon.com/ec2/)
- [RDS User Guide](https://docs.aws.amazon.com/rds/)
