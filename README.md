# Setting Up an Expense Tracker in AWS: Step-by-Step Guide

This guide will walk you through the process of setting up and deploying the Expense Tracker application using AWS services, from initial setup to final deployment.

## Prerequisites
- AWS account
- Basic understanding of PHP, HTML, and CSS
- Familiarity with AWS services like EC2 and DynamoDB

## Step 1: Create a DynamoDB Table

1. Log in to the AWS Management Console
2. Navigate to DynamoDB
3. Click "Create table"
4. Configure the table:
   - Table name: `tasks` (as specified in the code)
   - Partition key: `id` (Number)
   - Leave other settings as default
5. Click "Create table" and wait for the table to become active

## Step 2: Set Up an EC2 Instance

1. Navigate to EC2 in the AWS Management Console
2. Click "Launch Instance"
3. Configure your instance:
   - Name: `ExpenseTrackerServer`
   - Select Amazon Linux 2 AMI
   - Choose t2.micro instance type (free tier eligible)
   - Create or select an existing key pair for SSH access
   - Configure security group to allow:
     - SSH (port 22) from your IP
     - HTTP (port 80) from anywhere
     - HTTPS (port 443) from anywhere
4. Click "Launch Instance"

## Step 3: Connect to Your EC2 Instance

1. Navigate to EC2 > Instances
2. Select your instance
3. Click "Connect"
4. Choose "SSH client" and follow the instructions to connect using your key pair

## Step 4: Set Up the Web Server

1. Update the package repositories:
   ```bash
   sudo yum update -y
   ```

2. Install Apache web server, PHP, and required extensions:
   ```bash
   sudo yum install -y httpd php php-cli php-curl php-json
   ```

3. Start and enable the Apache service:
   ```bash
   sudo systemctl start httpd
   sudo systemctl enable httpd
   ```

4. Install Composer (PHP package manager):
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

## Step 5: Create an IAM Role for EC2

1. Navigate to IAM in the AWS Console
2. Go to "Roles" and click "Create role"
3. Choose "AWS service" as the trusted entity and "EC2" as the use case
4. Attach the following policies:
   - `AmazonDynamoDBFullAccess` (for production, use a more restricted policy)
5. Name the role `EC2DynamoDBAccess` and create it

## Step 6: Attach the IAM Role to Your EC2 Instance

1. Go to EC2 > Instances
2. Select your instance
3. Click "Actions" > "Security" > "Modify IAM role"
4. Select the `EC2DynamoDBAccess` role
5. Click "Update IAM role"

## Step 7: Set Up the Project Files

1. Navigate to the Apache web directory:
   ```bash
   cd /var/www/html/
   ```

2. Create a project directory and navigate to it:
   ```bash
   sudo mkdir expense-tracker
   sudo chown ec2-user:ec2-user expense-tracker
   cd expense-tracker
   ```

3. Initialize a new Composer project:
   ```bash
   composer init --no-interaction
   ```

4. Install the AWS SDK for PHP:
   ```bash
   composer require aws/aws-sdk-php
   ```

5. Create the main application files:

   - Create `index.php` file:
     ```bash
     nano index.php
     ```
     The PHP and HTML code goes here, then save and exit (Ctrl+X, Y, Enter)

   - Create `styles.css` file:
     ```bash
     nano styles.css
     ```
     The CSS code goes here, then save and exit

6. Set the correct permissions:
   ```bash
   sudo chown -R apache:apache /var/www/html/expense-tracker
   sudo chmod -R 755 /var/www/html/expense-tracker
   ```

## Step 8: Configure AWS Region

1. Confirm the region in your `index.php` file matches your AWS region
   ```php
   $client = new DynamoDbClient([
       'region' => 'ap-south-1', // Make sure this matches your AWS region
       'version' => 'latest',
   ]);
   ```

2. If needed, update the region to match where you created the DynamoDB table

## Step 9: Test Your Application

1. Get your EC2 instance's public IP or DNS name from the EC2 console
2. Open a web browser and navigate to: `http://your-ec2-public-ip/expense-tracker`
3. You should see the Expense Tracker application interface
4. Test the application by:
   - Adding an expense
   - Updating an expense
   - Deleting an expense

By following these steps, you'll have a fully functional Expense Tracker application deployed on AWS using EC2 for hosting and DynamoDB for the database backend.
