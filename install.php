<?php
// install.php - Fixed Database Installation Script
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shisha Lounge - Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .step { margin: 15px 0; padding: 10px; border-left: 4px solid #007bff; }
        h1 { color: #333; text-align: center; }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .btn:hover { background: #0056b3; }
        .config-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚬 Shisha Lounge Management System</h1>
        <h2>Database Installation & Setup</h2>

<?php
// Check if installation form was submitted
if ($_POST && isset($_POST['install'])) {
    
    // Get database configuration from form
    $host = $_POST['host'] ?? 'localhost';
    $dbname = $_POST['dbname'] ?? 'shisha_lounge_db';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    
    echo "<div class='step'><h3>🔧 Kuanza Installation...</h3></div>";
    
    try {
        // First, connect without database to create it
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='step success'>✅ Muunganisho wa MySQL umefanikiwa!</div>";
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='step success'>✅ Database '$dbname' imetengenezwa!</div>";
        
        // Now connect to the specific database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='step'><h3>📊 Kutengeneza Meza za Database...</h3></div>";
        
        // Create customers table
        $sql = "CREATE TABLE IF NOT EXISTS customers (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            visits INT(11) DEFAULT 1,
            last_visit DATE NOT NULL,
            favorite_flavor VARCHAR(50) DEFAULT NULL,
            total_spent DECIMAL(10,2) DEFAULT 0.00,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ Jedwali la wateja limetengenezwa!</div>";
        
        // Create flavors table
        $sql = "CREATE TABLE IF NOT EXISTS flavors (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            stock INT(11) NOT NULL,
            min_stock INT(11) DEFAULT 10,
            price DECIMAL(10,2) NOT NULL,
            popularity INT(11) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ Jedwali la flavors limetengenezwa!</div>";
        
        // Create inventory table
        $sql = "CREATE TABLE IF NOT EXISTS inventory (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            item_name VARCHAR(100) NOT NULL,
            stock INT(11) NOT NULL,
            unit VARCHAR(20) NOT NULL,
            min_stock INT(11) DEFAULT 10,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ Jedwali la inventory limetengenezwa!</div>";
        
        // Create payments table
        $sql = "CREATE TABLE IF NOT EXISTS payments (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('Cash', 'M-Pesa', 'Card') NOT NULL,
            transaction_date DATE NOT NULL,
            transaction_time TIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ Jedwali la malipo limetengenezwa!</div>";
        
        // Create employees table
        $sql = "CREATE TABLE IF NOT EXISTS employees (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(50) NOT NULL,
            shift ENUM('Mchana', 'Jioni', 'Usiku') NOT NULL,
            services_count INT(11) DEFAULT 0,
            salary DECIMAL(10,2) NOT NULL,
            hire_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('active', 'inactive') DEFAULT 'active'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ Jedwali la wafanyakazi limetengenezwa!</div>";
        
        echo "<div class='step'><h3>📝 Kuongeza Data ya Mfano...</h3></div>";
        
        // Clear existing data
        $pdo->exec("DELETE FROM customers");
        $pdo->exec("DELETE FROM flavors");
        $pdo->exec("DELETE FROM inventory");
        $pdo->exec("DELETE FROM payments");
        $pdo->exec("DELETE FROM employees");
        
        // Reset auto increment
        $pdo->exec("ALTER TABLE customers AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE flavors AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE inventory AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE payments AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE employees AUTO_INCREMENT = 1");
        
        // Insert sample flavors
        $flavors = [
            ['Double Apple', 25, 10, 15000, 85],
            ['Mint', 8, 10, 12000, 70],
            ['Watermelon', 18, 15, 18000, 90],
            ['Lemon', 5, 10, 14000, 45],
            ['Grape', 22, 12, 16000, 65],
            ['Blue Mint', 15, 10, 13000, 75],
            ['Orange', 20, 12, 14500, 60]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO flavors (name, stock, min_stock, price, popularity) VALUES (?, ?, ?, ?, ?)");
        foreach ($flavors as $flavor) {
            $stmt->execute($flavor);
        }
        echo "<div class='step success'>✅ Sample flavors zimeongezwa!</div>";
        
        // Insert sample inventory
        $inventory = [
            ['Mkaa (Charcoal)', 45, 'kg', 20],
            ['Foil', 150, 'vipande', 50],
            ['Pipes', 12, 'vipande', 8],
            ['Mouth Tips', 200, 'vipande', 100],
            ['Coal Tongs', 8, 'vipande', 5],
            ['Cleaning Brushes', 15, 'vipande', 10]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO inventory (item_name, stock, unit, min_stock) VALUES (?, ?, ?, ?)");
        foreach ($inventory as $item) {
            $stmt->execute($item);
        }
        echo "<div class='step success'>✅ Sample inventory imeongezwa!</div>";
        
        // Insert sample employees
        $employees = [
            ['Mohamed Ali', 'Senior Waiter', 'Mchana', 28, 400000],
            ['Grace Mwangi', 'Waiter', 'Jioni', 22, 350000],
            ['Hassan Omari', 'Cashier', 'Mchana', 35, 450000],
            ['Amina Hassan', 'Waiter', 'Usiku', 15, 320000],
            ['John Mwalimu', 'Security', 'Jioni', 10, 280000]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO employees (name, position, shift, services_count, salary) VALUES (?, ?, ?, ?, ?)");
        foreach ($employees as $employee) {
            $stmt->execute($employee);
        }
        echo "<div class='step success'>✅ Sample employees wameongezwa!</div>";
        
        // Insert sample customers and payments
        $customers = [
            ['Ahmed Hassan', 12, '2025-06-24', 'Double Apple', 15000],
            ['Fatuma Said', 8, '2025-06-23', 'Mint', 12000],
            ['John Mwalimu', 5, '2025-06-22', 'Watermelon', 18000],
            ['Maryam Ali', 15, '2025-06-24', 'Blue Mint', 13000],
            ['Hassan Omar', 3, '2025-06-21', 'Grape', 16000]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO customers (name, visits, last_visit, favorite_flavor, total_spent) VALUES (?, ?, ?, ?, ?)");
        foreach ($customers as $customer) {
            $stmt->execute($customer);
        }
        echo "<div class='step success'>✅ Sample customers wameongezwa!</div>";
        
        // Insert sample payments
        $payments = [
            ['Ahmed Hassan', 15000, 'M-Pesa', '2025-06-24', '14:30:00'],
            ['Fatuma Said', 12000, 'Cash', '2025-06-24', '15:45:00'],
            ['John Mwalimu', 18000, 'M-Pesa', '2025-06-24', '16:20:00'],
            ['Maryam Ali', 13000, 'Card', '2025-06-24', '17:15:00'],
            ['Hassan Omar', 16000, 'Cash', '2025-06-23', '19:30:00']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO payments (customer_name, amount, payment_method, transaction_date, transaction_time) VALUES (?, ?, ?, ?, ?)");
        foreach ($payments as $payment) {
            $stmt->execute($payment);
        }
        echo "<div class='step success'>✅ Sample payments zimeongezwa!</div>";
        
        // Now update the config file
        $configContent = "<?php
// config/database.php - Database Configuration
class Database {
    private \$host = \"$host\";
    private \$database_name = \"$dbname\";
    private \$username = \"$username\";
    private \$password = \"$password\";
    private \$connection;

    public function connect() {
        \$this->connection = null;
        
        try {
            \$this->connection = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->database_name . \";charset=utf8mb4\",
                \$this->username,
                \$this->password
            );
            \$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException \$exception) {
            echo \"Connection error: \" . \$exception->getMessage();
        }
        
        return \$this->connection;
    }
}
?>";
        
        // Create config directory if it doesn't exist
        if (!file_exists('config')) {
            mkdir('config', 0755, true);
        }
        
        // Write config file
        file_put_contents('config/database.php', $configContent);
        echo "<div class='step success'>✅ Configuration file imetengenezwa!</div>";
        
        echo "<div class='step success'><h3>🎉 Installation Imekamilika!</h3></div>";
        echo "<div class='step'>";
        echo "<p><strong>Database:</strong> $dbname</p>";
        echo "<p><strong>Tables Created:</strong> 5</p>";
        echo "<p><strong>Sample Data:</strong> Imeongezwa</p>";
        echo "<p><strong>Ready to Use:</strong> ✅</p>";
        echo "</div>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>";
        echo "<a href='index.php' class='btn'>🚀 Fungua Mfumo</a>";
        echo "<a href='api.php?action=get_customers' class='btn'>🧪 Test API</a>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='step error'>❌ Hitilafu: " . $e->getMessage() . "</div>";
        echo "<div class='step error'>Tafadhali angalia:</div>";
        echo "<ul>";
        echo "<li>MySQL server inafanya kazi?</li>";
        echo "<li>Username na password ni sahihi?</li>";
        echo "<li>User ana permissions za kutengeneza database?</li>";
        echo "</ul>";
    }
    
} else {
    // Show configuration form
    ?>
    
    <div class="step">
        <h3>🔧 Hatua ya 1: Database Configuration</h3>
        <p>Ingiza taarifa za database yako hapo chini:</p>
    </div>
    
    <form method="POST" class="config-form">
        <div class="form-group">
            <label for="host">Database Host:</label>
            <input type="text" id="host" name="host" value="localhost" required>
        </div>
        
        <div class="form-group">
            <label for="dbname">Database Name:</label>
            <input type="text" id="dbname" name="dbname" value="shisha_lounge_db" required>
        </div>
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="root" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Ingiza password (achana tupu kama hakuna)">
        </div>
        
        <button type="submit" name="install" class="btn">🚀 Anza Installation</button>
    </form>
    
    <div class="step warning">
        <h4>⚠️ Muhimu:</h4>
        <ul>
            <li>Hakikisha MySQL server inafanya kazi</li>
            <li>User ana permissions za kutengeneza database</li>
            <li>Kama database tayari ipo, itaongezwa data mpya</li>
        </ul>
    </div>
    
    <div class="step">
        <h4>📋 Kile kitakachotengenezwa:</h4>
        <ul>
            <li>✅ Database mpya (ama kutumia iliyopo)</li>
            <li>✅ Meza 5: customers, flavors, inventory, payments, employees</li>
            <li>✅ Sample data kwa testing</li>
            <li>✅ Configuration file</li>
        </ul>
    </div>
    
    <?php
}
?>

    </div>
</body>
</html>