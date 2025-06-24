<?php
// test-connection.php - Debug Connection Issues
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

echo "<h2>🔧 Shisha Lounge - Connection Debug Test</h2>";

// Test 1: Basic PHP
echo "<h3>1. PHP Status</h3>";
echo "✅ PHP Version: " . phpversion() . "<br>";
echo "✅ Current Time: " . date('Y-m-d H:i:s') . "<br>";

// Test 2: Check if files exist
echo "<h3>2. File Structure Check</h3>";
$files = [
    'config/database.php',
    'api.php',
    'models/Customer.php',
    'models/Flavor.php',
    'models/Inventory.php',
    'models/Payment.php',
    'models/Employee.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file MISSING<br>";
    }
}

// Test 3: Database Connection
echo "<h3>3. Database Connection Test</h3>";
try {
    if (file_exists('config/database.php')) {
        include_once 'config/database.php';
        $database = new Database();
        $db = $database->connect();
        
        if ($db) {
            echo "✅ Database connection successful<br>";
            
            // Test database tables
            $tables = ['customers', 'flavors', 'inventory', 'payments', 'employees'];
            foreach ($tables as $table) {
                try {
                    $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
                    $result = $stmt->fetch();
                    echo "✅ Table '$table': {$result['count']} records<br>";
                } catch (Exception $e) {
                    echo "❌ Table '$table': Error - " . $e->getMessage() . "<br>";
                }
            }
        } else {
            echo "❌ Database connection failed<br>";
        }
    } else {
        echo "❌ Database config file missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}

// Test 4: API Endpoint Test
echo "<h3>4. API Endpoint Test</h3>";
if (file_exists('api.php')) {
    echo "✅ api.php exists<br>";
    
    // Test simple API call
    try {
        // Simulate GET request
        $_GET['action'] = 'get_customers';
        
        ob_start();
        include 'api.php';
        $api_output = ob_get_clean();
        
        if (!empty($api_output)) {
            echo "✅ API responds with data<br>";
            echo "📋 Sample API output: " . substr($api_output, 0, 100) . "...<br>";
        } else {
            echo "❌ API returns empty response<br>";
        }
    } catch (Exception $e) {
        echo "❌ API Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ api.php file missing<br>";
}

// Test 5: CORS Headers
echo "<h3>5. CORS Headers Test</h3>";
echo "✅ Access-Control-Allow-Origin: " . (headers_list() ? 'Set' : 'Not Set') . "<br>";

// Test 6: Server Configuration
echo "<h3>6. Server Configuration</h3>";
echo "📂 Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "🌐 Server Name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "📁 Current Directory: " . getcwd() . "<br>";
echo "🔗 Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Test 7: JSON Test
echo "<h3>7. JSON Response Test</h3>";
$test_data = [
    'status' => 'success',
    'message' => 'Connection test successful',
    'timestamp' => date('Y-m-d H:i:s'),
    'data' => [
        'customers' => 5,
        'flavors' => 7,
        'test' => true
    ]
];

echo "📤 JSON Output:<br>";
echo "<pre>" . json_encode($test_data, JSON_PRETTY_PRINT) . "</pre>";
?>

<hr>
<h3>🔧 Quick Fixes:</h3>
<ol>
    <li><strong>If database connection fails:</strong>
        <ul>
            <li>Make sure XAMPP/WAMP MySQL is running</li>
            <li>Check username/password in config/database.php</li>
            <li>Run install.php again</li>
        </ul>
    </li>
    
    <li><strong>If files are missing:</strong>
        <ul>
            <li>Make sure all files are in the correct folder</li>
            <li>Check file permissions</li>
        </ul>
    </li>
    
    <li><strong>If API doesn't respond:</strong>
        <ul>
            <li>Check PHP error logs</li>
            <li>Make sure .htaccess allows PHP execution</li>
        </ul>
    </li>
</ol>

<h3>📞 Next Steps:</h3>
<p>1. Fix any ❌ errors shown above</p>
<p>2. Test this URL: <a href="api.php?action=get_customers">api.php?action=get_customers</a></p>
<p>3. If all ✅, then <a href="index.html">open main system</a></p>