<?php 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
require 'vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;
// Initialize DynamoDB Client
$client = new DynamoDbClient([
 'region' => 'ap-south-1',
 'version' => 'latest',
]);
$tableName = 'tasks';
// Function to add an expense
function addExpense($client, $tableName, $id, $description, $amount, $category, 
$date) {
 try {
 $result = $client->putItem([
 'TableName' => $tableName,
 'Item' => [
 'id' => ['N' => (string)$id],
 'description' => ['S' => $description],
 'amount' => ['N' => (string)$amount],
 'category' => ['S' => $category],
 'date' => ['S' => $date],
 ],
 ]);
 echo "<p class='alert success'>Expense added successfully.</p>";
 } catch (AwsException $e) {
 echo "<p class='alert error'>Unable to add expense. Error: " . $e-
>getMessage() . "</p>";
 }
}
// Function to view expenses
function viewExpenses($client, $tableName) {
 try {
 $result = $client->scan([
 'TableName' => $tableName,
 ]);
 echo "<div class='expense-list'>";
 $total = 0;
 foreach ($result['Items'] as $item) {
 $id = $item['id']['N'];
 $description = $item['description']['S'];
 $amount = $item['amount']['N'];
 $category = $item['category']['S'];
 $date = $item['date']['S'];
 $total += (float)$amount;
 echo "<div class='expense-item'>";
 echo "<div>ID: $id</div>";
 echo "<div>Description: $description</div>";
 echo "<div>Amount: ₹$amount</div>";
 echo "<div>Category: $category</div>";
 echo "<div>Date: $date</div>";
 echo "</div>";
 }
 echo "</div>";
 echo "<h3>Total Expenses: ₹" . number_format($total, 2) . "</h3>";
 } catch (AwsException $e) {
 echo "<p class='alert error'>Unable to fetch expenses. Error: " . $e-
>getMessage() . "</p>";
 }
}
// Function to update an expense
function updateExpense($client, $tableName, $id, $description, $amount, 
$category, $date) {
 try {
 $result = $client->updateItem([
 'TableName' => $tableName,
 'Key' => [
 'id' => ['N' => (string)$id],
 ],
 'UpdateExpression' => 'SET description = :desc, amount = :amt, category = 
:cat, date = :dt',
 'ExpressionAttributeValues' => [
 ':desc' => ['S' => $description],
 ':amt' => ['N' => (string)$amount],
 ':cat' => ['S' => $category],
 ':dt' => ['S' => $date],
 ],
 ]);
 echo "<p class='alert success'>Expense updated successfully.</p>";
 } catch (AwsException $e) {
 echo "<p class='alert error'>Unable to update expense. Error: " . $e-
>getMessage() . "</p>";
 }
}
// Function to delete an expense
function deleteExpense($client, $tableName, $id) {
 try {
 $result = $client->deleteItem([
 'TableName' => $tableName,
 'Key' => [
 'id' => ['N' => (string)$id],
 ],
 ]);
 echo "<p class='alert success'>Expense deleted successfully.</p>";
 } catch (AwsException $e) {
 echo "<p class='alert error'>Unable to delete expense. Error: " . $e-
>getMessage() . "</p>";
 }
}
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (isset($_POST['add'])) {
 addExpense($client, $tableName, $_POST['id'], $_POST['description'], 
$_POST['amount'], $_POST['category'], $_POST['date']);
 } elseif (isset($_POST['update'])) {
 updateExpense($client, $tableName, $_POST['id'], $_POST['description'], 
$_POST['amount'], $_POST['category'], $_POST['date']);
 } elseif (isset($_POST['delete'])) {
 deleteExpense($client, $tableName, $_POST['id']);
 }
}
viewExpenses($client, $tableName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Expense Tracker</title>
 <link rel="stylesheet" href="styles.css">
</head>
<body>
 <h1>Expense Tracker</h1>
 <form method="POST" class="expense-form">
 <label for="id">ID:</label>
 <input type="number" name="id" id="id" required><br>
 <label for="description">Description:</label>
 <input type="text" name="description" id="description" required><br>
 <label for="amount">Amount:</label>
 <input type="number" name="amount" id="amount" step="0.01" 
required><br>
 <label for="category">Category:</label>
 <input type="text" name="category" id="category" required><br>
 <label for="date">Date:</label>
 <input type="date" name="date" id="date" required><br>
 <button type="submit" name="add">Add Expense</button>
 <button type="submit" name="update">Update Expense</button>
 <button type="submit" name="delete">Delete Expense</button>
 </form>
 <div class="expense-list">
 <!-- Expenses will be displayed here -->
 </div>
</body>
</html>
